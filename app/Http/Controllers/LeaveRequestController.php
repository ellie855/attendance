<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\LeaveType;
use Illuminate\Validation\Rule;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Notifications\NewLeaveRequest;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $requests = auth()->user()
            ->leaveRequests()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('leave-requests.index', [
            'requests' => $requests,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('leave-requests.create', [
            'types' => LeaveType::cases(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::enum(LeaveType::class)],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        // 変更点①: createの戻り値を変数に受け取る
        $newRequest = auth()->user()->leaveRequests()->create($validated);

        // 変更点②: 管理者全員に通知
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewLeaveRequest($newRequest));
        }

        return redirect()->route('leave-requests.index')
            ->with('success', '休暇申請を送信しました');
    }

    /**
     * Display the specified resource.
     */
    public function show(LeaveRequest $leaveRequest)
    {
        return view('leave-requests.show', [
            'leaveRequest' => $leaveRequest,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LeaveRequest $leaveRequest)
    {
        return view('leave-requests.edit', [
            'leaveRequest' => $leaveRequest,
            'types' => LeaveType::cases(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::enum(LeaveType::class)],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        $leaveRequest->update($validated);

        return redirect()->route('leave-requests.show', $leaveRequest)
            ->with('success', '休暇申請を更新しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeaveRequest $leaveRequest)
    {
        $leaveRequest->delete();

        return redirect()->route('leave-requests.index')
           ->with('success', '休暇申請を取り下げました');
    }
}
