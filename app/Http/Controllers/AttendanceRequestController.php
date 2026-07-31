<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceCorrectionRequest;
use App\Models\User;
use App\Notifications\NewCorrectionRequest;
use App\Enums\CorrectionRequestType;
use App\Enums\AttendanceType;

class AttendanceRequestController extends Controller
{
    public function index()
    {
        $requests = auth()->user()
            ->correctionRequests()
            ->with('targetAttendance')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('attendance-requests.index', [
            'requests' => $requests,
        ]);
    }

    public function create()
    {
        $user = auth()->user();
        // 自分の過去１４日分の打刻を取得(修正の対象候補)
        $attendances = $user->attendances()
        ->where('created_at', '>=', now()->subDays(14))
        ->orderBy('created_at', 'desc')
        ->get();

        return view('attendance-requests.create', [
            'attendances'  => $attendances,
            'requestTypes' => CorrectionRequestType::cases(),
            'clockTypes'   => AttendanceType::cases(),
        ]);
    }

    public function store(Request $request)
    {
        // 共通バリデーション
        $validated = $request->validate([
            'type'     => 'required|in:modify,add',
            'new_time' => 'required|date',
            'reason'   => 'required|string|max:1000',
        ]);

        // 種別ごとに追加バリデーション
        if ($validated['type'] === 'modify') {
            $extra = $request->validate([
                'target_attendance_id' => 'required|exists:attendances,id',
            ]);
            $data = [
                'type'                 => 'modify',
                'target_attendance_id' => $extra['target_attendance_id'],
                'clock_type'           => null,
                'new_time'             => $validated['new_time'],
                'reason'               => $validated['reason'],
            ];
        } else {
            // add タイプ
            $extra = $request->validate([
                'clock_type' => 'required|in:clock_in,clock_out,break_start,break_end',
            ]);
            $data = [
                'type'                 => 'add',
                'target_attendance_id' => null,
                'clock_type'           => $extra['clock_type'],
                'new_time'             => $validated['new_time'],
                'reason'               => $validated['reason'],
            ];
        }

        $newRequest = auth()->user()->correctionRequests()->create($data);

        //　全管理者に通知
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewCorrectionRequest($newRequest));
        }

        return redirect()->route('attendance-requests.index')
            ->with('success', '申請を送信しました');
    }

    public function show(AttendanceCorrectionRequest $attendanceRequest)
    {
        //　自分の申請のみ表示可能（念のため）
        abort_if($attendanceRequest->user_id !== auth()->id(), 403);

        return view('attendance-requests.show', [
            'attendanceRequest' => $attendanceRequest,
        ]);
    }

    public function edit(AttendanceCorrectionRequest $attendanceRequest)
    {
        $this->authorize('update', $attendanceRequest);

        $user = auth()->user();
        $attendances = $user->attendances()
            ->where('created_at', '>=', now()->subDays(14))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('attendance-requests.edit', [
            'attendanceRequest' => $attendanceRequest,
            'attendances'       => $attendances,
            'clockTypes'        => AttendanceType::cases(),
        ]);
    }

    public function update(Request $request, AttendanceCorrectionRequest $attendanceRequest)
    {
        $this->authorize('update', $attendanceRequest);

        // 共通バリデーション
        $validated = $request->validate([
            'new_time' => 'required|date',
            'reason' => 'required|string|max:1000',
        ]);

        // 種別ごとに追加バリデーション + 更新内容決定
        if ($attendanceRequest->type?->value === 'add') {
            $extra = $request->validate([
                'clock_type' => 'required|in:clock_in,clock_out,break_start,break_end',
            ]);
            $data = [
                'clock_type' => $extra['clock_type'],
                'new_time'   => $validated['new_time'],
                'reason'     => $validated['reason'],
            ];
        } else {
            $extra = $request->validate([
                'target_attendance_id' => 'required|exists:attendances,id',
            ]);
            $data = [
                'target_attendance_id' => $extra['target_attendance_id'],
                'new_time'             => $validated['new_time'],
                'reason'               => $validated['reason'],
            ];
        }

        $attendanceRequest->update($data);
            return redirect()->route('attendance-requests.index')
           ->with('success', '申請を更新しました');
    }

    public function destroy(AttendanceCorrectionRequest $attendanceRequest)
    {
        $this->authorize('delete', $attendanceRequest);

        $attendanceRequest->delete();

        return redirect()->route('attendance-requests.index')
            ->with('success', '申請を取り下げました');
    }
}
