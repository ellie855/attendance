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

        //今日の最新打刻から状態を判定
        $latest = $user->attendances()->whereDate('created_at',today())->latest()->first();
        if (!$latest) {
            $status = '未出勤';
        } elseif ($latest->type === AttendanceType::ClockIn) {
            $status = '勤務中';
        } elseif ($latest->type === AttendanceType::BreakStart) {
            $status = '休憩中';
        } elseif ($latest->type === AttendanceType::BreakEnd) {
            $status = '勤務中';
        } else {
            $status = '退勤済';
        }

        // 各退勤の勤務時間 + 進行中の出勤 ID リスト
        $durations = [];
        $ongoingClockIns = [];
        $lastClockInByUser = [];

        foreach ($attendances->sortBy('created_at') as $att) {
            $uid = $att->user_id;
            if ($att->type === AttendanceType::ClockIn) {
                $lastClockInByUser[$uid] = $att;
            } elseif ($att->type === AttendanceType::ClockOut && isset($lastClockInByUser[$uid])) {
                $secs = $lastClockInByUser[$uid]->created_at->diffInSeconds($att->created_at);
                $h = floor($secs / 3600);
                $m = floor(($secs % 3600) / 60);
                $durations[$att->id] = "{$h}時間{$m}分";
                unset($lastClockInByUser[$uid]);
            }
        }
        foreach ($lastClockInByUser as $att) {
            $ongoingClockIns[] = $att->id;
        }

        //　今月のサマリー計算
        $monthRecords = $user->attendances()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->orderBy('created_at')
            ->get();

        //今月の総勤務秒数　+　出勤日数
        $monthTotalSec = 0;
        $clockInTime = null;
        $workDates = [];
        foreach ($monthRecords as $r) {
            if ($r->type === AttendanceType::ClockIn) {
                $clockInTime = $r->created_at;
                $workDates[$r->created_at->format('Y-m-d')] = true;
            } elseif ($r->type === AttendanceType::ClockOut && $clockInTime) {
                $monthTotalSec += $clockInTime->diffInSeconds($r->created_at);
                $clockInTime = null;
            }
        }

        $monthHours = floor($monthTotalSec / 3600);
        $monthDays  = count($workDates);
        $avgHours   = $monthDays > 0 ? round($monthHours / $monthDays, 1) : 0;

        //　連続出勤日数　（今日から遡って）
        $streak = 0;
        $checkDate = today();
        while (isset($workDates[$checkDate->format('Y-m-d')])) {
            $streak++;
            $checkDate = $checkDate->subDay();
        }

        $summary = [
            'total_hours' => $monthHours,
            'days'        => $monthDays,
            'avg_hours'   => $avgHours,
            'streak'      => $streak, 
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
}