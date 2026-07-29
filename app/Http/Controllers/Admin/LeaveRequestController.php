<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Notifications\LeaveRequestProcessed;

class LeaveRequestController extends Controller
{
    public function index()
    {
         $requests = LeaveRequest::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.leave-requests.index', [
            'requests' => $requests,
        ]);
    }

    // 承認処理
    public function approve(LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== 'pending') {
            return redirect()->route('admin.leave-requests.index')
                ->with('error', 'この申請は既に処理済みです');
        }

        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // 申請者に通知
        $leaveRequest->user->notify(new LeaveRequestProcessed($leaveRequest));

        return redirect()->route('admin.leave-requests.index')
            ->with('success', "{$leaveRequest->user->name} の申請を承認しました");
    }

    // 却下処理
    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== 'pending') {
            return redirect()->route('admin.leave-requests.index')
                ->with('error', 'この申請は既に処理済みです');
        }

        $validated = $request->validate([
            'admin_comment' => 'nullable|string|max:1000',
        ]);

        $leaveRequest->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'admin_comment' => $validated['admin_comment'] ?? null,
        ]);

        // 申請者に通知
        $leaveRequest->user->notify(new LeaveRequestProcessed($leaveRequest));

        return redirect()->route('admin.leave-requests.index')
            ->with('success', "{$leaveRequest->user->name} の申請を却下しました");
    }
}