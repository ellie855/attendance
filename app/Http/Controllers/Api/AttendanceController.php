<?php

namespace App\Http\Controllers\Api;

use App\Enums\AttendanceType;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Services\AttendanceSummaryService;
use Illuminate\Http\Request;
use App\Http\Resources\AttendanceResource;

class AttendanceController extends Controller
{
    /**
     * 自分の勤怠履歴を取得
     * GET /api/attendances
     */
    public function index(Request $request)
    {
        $attendances = $request->user()
            ->attendances()
            ->latest()
            ->limit(50)
            ->get();

        return AttendanceResource::collection($attendances);
    }

    /** 
    * 出勤打刻
    * POST /api/attendances/clock-in 
    */
    public function clockIn(Request $request)
    {
        $attendance = Attendance::create([
            'user_id' => $request->user()->id,
            'type'    => AttendanceType::ClockIn,
        ]);

        return response()->json([
            'message'     => '出勤しました',
            'attendance'  => new AttendanceResource($attendance),
        ], 201);
    }

    /**
     * 退勤打刻
     * POST /api/attendances/clock-out
     */
    public function clockOut(Request $request)
    {
        $attendance = Attendance::create([
            'user_id' => $request->user()->id,
            'type'    => AttendanceType::ClockOut,
        ]);

        return response()->json([
            'message'     => '退勤しました',
            'attendance'  => new AttendanceResource($attendance),
        ], 201);
    }

    /**
     * 休憩開始
     * POST /api/attendances/break-start
     */
    public function breakStart(Request $request)
    {
        $attendance = Attendance::create([
            'user_id' => $request->user()->id,
            'type'    => AttendanceType::BreakStart,
        ]);

        return response()->json([
            'message'    => '休憩を開始しました',
            'attendance' => new AttendanceResource($attendance),
        ], 201);
    }

    /**
     * 休憩終了
     * POST /api/attendances/break-end
     */
    public function breakEnd(Request $request)
    {
        $attendance = Attendance::create([
            'user_id' => $request->user()->id,
            'type'    => AttendanceType::BreakEnd,
        ]);

        return response()->json([
            'message'    => '休憩を終了しました',
            'attendance' => new AttendanceResource($attendance),
        ], 201);
    }

    /**
     * 今月/今週のサマリー
     * GET /api/summary
     */
    public function summary(Request $request, AttendanceSummaryService $service)
    {
        $user = $request->user();

        // 今月
        $monthRecords = $user->attendances()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->orderBy('created_at')
            ->get();
        $monthSec = $service->calculateWorkSeconds($monthRecords);

        // 今週
        $weekRecords = $user->attendances()
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->orderBy('created_at')
            ->get();
        $weekSec = $service->calculateWorkSeconds($weekRecords);

        return response()->json([
            'month' => [
                'total_seconds' => $monthSec,
                'total_hours'   => intdiv($monthSec, 3600),
                'total_minutes' => intdiv($monthSec % 3600, 60),
            ],
            'week' => [
                'total_seconds' => $weekSec,
                'total_hours'   => intdiv($weekSec, 3600),
                'total_minutes' => intdiv($weekSec %3600, 60),
            ],
        ]);
    }
}
