<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceCorrectionRequest;
use App\Notifications\RequestProcessed;

class AttendanceRequestController extends Controller
{   // 未処理の申請一覧を表示
    public function index()
    {
        $requests = AttendanceCorrectionRequest::with(['user', 'targetAttendance'])
            ->where('status', 'pending')
            ->orderBy('created_at','asc')
            ->get();

        return view('admin.attendance-requests.index', [
            'requests' => $requests,
        ]);
    }

    // 申請を承認する
    public function approve(AttendanceCorrectionRequest $attendanceRequest)
    {
        // 既に承認/却下済みならエラー
        if ($attendanceRequest->status !== 'pending') {
            return redirect()->route('admin.attendance-requests.index')
                ->with('error', 'この申請は既に処理済みです');
        }

        //　申請された時刻に上書き
        $attendanceRequest->targetAttendance->update([
            'created_at' => $attendanceRequest->new_time,
        ]);

        $attendanceRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' =>now(),
        ]);

        // 申請者に通知
        $attendanceRequest->user->notify(new RequestProcessed($attendanceRequest));

        return redirect()->route('admin.attendance-requests.index')
            ->with('success', "{$attendanceRequest->user->name} の申請を承認しました");
    }

    // 申請を却下する
    public function reject(Request $request, AttendanceCorrectionRequest $attendanceRequest)
    {
        if($attendanceRequest->status !== 'pending') {
            return redirect()->route('admin.attendance-requests.index')
                ->with('error', 'この申請は既に申請済みです');
        }

        $validated = $request->validate([
            'admin_comment' => 'nullable|string|max:1000',
        ]);

        $attendanceRequest->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),

            'admin_comment' => $validated['admin_comment'] ?? null,
        ]);

        // 申請者に通知
        $attendanceRequest->user->notify(new RequestProcessed($attendanceRequest));

        return redirect()->route('admin.attendance-requests.index')
            ->with('success', "{$attendanceRequest->user->name} の申請を却下しました");
    }
}
