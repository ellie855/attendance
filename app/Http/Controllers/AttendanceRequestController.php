<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceCorrectionRequest;

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
            'attendances' => $attendances,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'target_attendance_id' => 'required|exists:attendances,id',
            'new_time' => 'required|date',
            'reason' => 'required|string|max:1000',
        ]);
        auth()->user()->correctionRequests()->create([
            'target_attendance_id' => $validated['target_attendance_id'],
            'new_time' => $validated['new_time'],
            'reason' => $validated['reason'],
        ]);    
        return redirect()->route('attendance-requests.index')
            ->with('success', '申請を送信しました');
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
            'attendances' => $attendances,
        ]);
    }

    public function update(Request $request, AttendanceCorrectionRequest $attendanceRequest)
    {
        $this->authorize('update', $attendanceRequest);

        $validated = $request->validate([
            'target_attendance_id' => 'required|exists:attendances,id',
            'new_time' => 'required|date',
            'reason' => 'required|string|max:1000',
        ]);

        $attendanceRequest->update($validated);

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
