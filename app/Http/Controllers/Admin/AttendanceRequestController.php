<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceCorrectionRequest;

class AttendanceRequestController extends Controller
{
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

    public function approve(AttendanceCorrectionRequest $attendanceRequest)
    {
        if ($attendanceRequest->status !== 'pending') {
            return redirect()->route('admin.attendance-requests.index')
                ->with('error', 'この申請は既に処理済みです');
        }

        $attendanceRequest->targetAttendance->update([
            'created_at' => $attendanceRequest->new_time,
        ]);

        $attendanceRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' =>now(),
        ]);

        return redirect()->route('admin.attendance-requests.index')
            ->with('success', "{$attendanceRequest->user->name} の申請を承認しました");
    }

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

        return redirect()->route('admin.attendance-requests.index')
            ->with('success', "{$attendanceRequest->user->name} の申請を却下しました");
    }
}
