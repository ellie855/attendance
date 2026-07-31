<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Enums\AttendanceType;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $todayNote = $user->dailyNotes()->whereDate('date', today())->first();
        //管理者なら全員、それ以外は自分の打刻のみ
        if ($user->isAdmin()){
            $attendances = Attendance::with('user')->latest()->get();
        } else{
            $attendances = $user->attendances()->latest()->get();
        }

        //最新打刻（全期間）から状態を判定
        $latest = $user->attendances()->latest()->first();
        if (!$latest) {
            $status = '未出勤';
        } elseif ($latest->type === AttendanceType::ClockIn) {
            $status = '勤務中';
        } elseif ($latest->type === AttendanceType::BreakStart) {
            $status = '休憩中';
        } elseif ($latest->type === AttendanceType::BreakEnd) {
            $status = '勤務中';
        } else {
            // ClockOut: 今日なら「退勤済」、それ以前なら「未出勤」扱い
            $status = $latest->created_at->isToday() ? '退勤済' : '未出勤';
        }

        // 各退勤の勤務時間 + 進行中の出勤 ID リスト
        $durations = [];
        $ongoingClockIns = [];
        $lastClockInByUser = [];
        $breakStartByUser = [];   // ユーザーごとの休憩開始時刻
        $breakSecByUser = [];     // ユーザーごとの休憩累計秒数

        foreach ($attendances->sortBy('created_at') as $att) {
            $uid = $att->user_id;
            if ($att->type === AttendanceType::ClockIn) {
                $lastClockInByUser[$uid] = $att;
                $breakSecByUser[$uid] = 0;
                $breakStartByUser[$uid] = null;
            } elseif ($att->type === AttendanceType::BreakStart && isset($lastClockInByUser[$uid])) {
                $breakStartByUser[$uid] = $att->created_at;
            } elseif ($att->type === AttendanceType::BreakEnd && !empty($breakStartByUser[$uid])) {
                $breakSecByUser[$uid] += $breakStartByUser[$uid]->diffInSeconds($att->created_at);
                $breakStartByUser[$uid] = null;
            } elseif ($att->type === AttendanceType::ClockOut && isset($lastClockInByUser[$uid])) {
                $secs = $lastClockInByUser[$uid]->created_at->diffInSeconds($att->created_at) - ($breakSecByUser[$uid] ?? 0);
                $h = floor($secs / 3600);
                $m = floor(($secs % 3600) / 60);
                $durations[$att->id] = "{$h}時間{$m}分";
                unset($lastClockInByUser[$uid]);
                unset($breakSecByUser[$uid]);
                unset($breakStartByUser[$uid]);
            }
        }
        foreach ($lastClockInByUser as $uid => $att) {
            $ongoingClockIns[$att->id] = [
                'clock_in_ts'     => $att->created_at->getTimestamp(),
                'break_sec'       => $breakSecByUser[$uid] ?? 0,
                'is_on_break'     => !empty($breakStartByUser[$uid]),
                'break_start_ts'  => !empty($breakStartByUser[$uid]) ? $breakStartByUser[$uid]->getTimestamp() : null,
            ];
        }

        //　今月のサマリー計算
        $monthRecords = $user->attendances()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->orderBy('created_at')
            ->get();

        //今月の総勤務秒数（休憩差引済み)
        $monthTotalSec = $this->calculateWorkSeconds($monthRecords);
        $monthHours = floor($monthTotalSec / 3600);

        // 出勤日数（streak 計算にも使う）
        $workDates = [];
        foreach ($monthRecords as $r) {
            if ($r->type === AttendanceType::ClockIn) {
                $workDates[$r->created_at->format('Y-m-d')] = true;
            }
        }

        $monthDays  = count($workDates);
        $avgHours   = $monthDays > 0 ? round($monthHours / $monthDays, 1) : 0;

        //　連続出勤日数　（今日から遡って）
        $streak = 0;
        $checkDate = today();
        while (isset($workDates[$checkDate->format('Y-m-d')])) {
            $streak++;
            $checkDate = $checkDate->subDay();
        }

        // 今週の総勤務時間（月～日）
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        $weekRecords = $user->attendances()
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->orderBy('created_at')
            ->get();

        // 今週の総勤務秒数（休憩差引済み）
        $weekTotalSec = $this->calculateWorkSeconds($weekRecords);

        $weekHours = floor($weekTotalSec / 3600);
        $weekMinutes = floor(($weekTotalSec % 3600) / 60);

        $summary = [
            'total_hours'  => $monthHours,
            'days'         => $monthDays,
            'avg_hours'    => $avgHours,
            'streak'       => $streak, 
            'week_hours'   => $weekHours,
            'week_minutes' => $weekMinutes,
        ];

        return view('attendances.index', [
            'attendances'     => $attendances,
            'status'          => $status,
            'userName'        => $user->name,
            'durations'       => $durations,
            'ongoingClockIns' => $ongoingClockIns,
            'summary'          => $summary,
            'todayNote' => $todayNote,
        ]);
    }    

    //出勤打刻
    public function clockIn()
    {
        Attendance::create([
            'user_id' => auth()->id(),
            'type'    => AttendanceType::ClockIn,
        ]);
        return redirect('/attendances');
    }

    //退勤打刻
    public function clockOut()
    {
        Attendance::create([
            'user_id' => auth()->id(),
            'type'    => AttendanceType::ClockOut,
        ]);
        return redirect('/attendances');
    }

    //休憩開始
    public function breakStart()
    {
        Attendance::create([
            'user_id' => auth()->id(),
            'type'    => AttendanceType::BreakStart,
        ]);
        return redirect('/attendances');
    }

    //休憩終了
    public function breakEnd()
    {
        Attendance::create([
            'user_id' => auth()->id(),
            'type'    => AttendanceType::BreakEnd,
        ]);
        return redirect('/attendances');
    }








    // 出勤確認画面
    public function confirmClockIn()
    {
        $user = auth()->user();
        $latest = $user->attendances()->latest()->first();
        $warning = null;
        if ($latest && $latest->type === AttendanceType::ClockIn) {
            $warning = '前回の出勤後、まだ退勤打刻がありません。本当に出勤しますか？';
        }
        return view('attendances.confirm', ['type'  => AttendanceType::ClockIn->value, 'warning' => $warning]);
    }

    // 退勤確認画面
    public function confirmClockOut()
    {
        $user = auth()->user();
        $latest = $user->attendances()->latest()->first();
        $warning = null;
        if (!$latest || $latest->type === AttendanceType::ClockOut){
            $warning = '直前の出勤打刻がありません。本当に退勤しますか？';
        }
        return view('attendances.confirm', ['type' => AttendanceType::ClockOut->value, 'warning' => $warning]);
    }

    //編集フォーム表示
    public function edit(Attendance $attendance)
    {
        $this->authorize('update', $attendance);
        return view('attendances.edit', ['attendance' => $attendance]);
    }

    //更新処理（勤怠修正の反映）
    public function update(UpdateAttendanceRequest $request, Attendance $attendance)
    {
        $attendance->update($request->validated());

        return redirect('/attendances');
    }

    //削除処理
    public function destroy(Attendance $attendance)
    {
        $this->authorize('delete', $attendance);
        $attendance->delete();
        return redirect('/attendances');
    }

    /**
     * 打刻レコード一覧から、休憩を差し引いた勤務秒数の合計を返す
     * （出勤～退勤の区間ごとに、その間の休憩時間を引いて集計）
     */
    private function calculateWorkSeconds($records): int
    {
        $total = 0;
        $clockIn = null;
        $breakStart = null;
        $breakSec = 0;

        foreach ($records as $r) {
            if ($r->type === AttendanceType::ClockIn) {
                $clockIn = $r->created_at;
                $breakSec = 0;
                $breakStart = null;
            } elseif ($r->type === AttendanceType::BreakStart && $clockIn) {
                $breakStart = $r->created_at;
            } elseif ($r->type === AttendanceType::BreakEnd && $breakStart) {
                $breakSec += $breakStart->diffInSeconds($r->created_at);
                $breakStart = null;
            } elseif ($r->type === AttendanceType::ClockOut && $clockIn) {
                $total += $clockIn->diffInSeconds($r->created_at) - $breakSec;
                $clockIn = null;
                $breakSec = 0;
                $breakStart = null;
            }
        }

        return $total;
    }
}