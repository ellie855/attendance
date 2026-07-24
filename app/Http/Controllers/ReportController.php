<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Enums\AttendanceType;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function monthly(Request $request)
    {
        $user = auth()->user();

        //　対象月(URLパラメータで指定、なければ今月)
        $ym = $request->query('ym', now()->format('Y-m'));
        $targetMonth = Carbon::createFromFormat('Y-m', $ym)->startOfMonth();

        // その月の全打刻を取得
        $attendances = $user->attendances()
            ->whereYear('created_at', $targetMonth->year)
            ->whereMonth('created_at', $targetMonth->month)
            ->orderBy('created_at', 'asc')
            ->get();

        //日ごとに集計
        $daysInMonth = $targetMonth->daysInMonth;
        $dailyReport = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $targetMonth->copy()->day($day);
            $dayAttendances = $attendances->filter(fn($a) => $a->created_at->day === $day);

            $clockIn = $dayAttendances->firstWhere('type', AttendanceType::ClockIn);
            $clockOut = $dayAttendances->where('type', AttendanceType::ClockOut)->last();

            //休憩時間の合計（秒）
            $breakSec = 0;
            $breakStart = null;
            foreach ($dayAttendances as $a) {
                if ($a->type === AttendanceType::BreakStart) {
                    $breakStart = $a->created_at;
                } elseif ($a->type === AttendanceType::BreakEnd && $breakStart) {
                    $breakSec += $breakStart->diffInSeconds($a->created_at);
                    $breakStart = null;
                }
            }

            //勤務時間（秒）
            $workSec = 0;
            if ($clockIn && $clockOut) {
                $workSec = $clockIn->created_at->diffInSeconds($clockOut->created_at) - $breakSec;
            }

            $dailyReport[] = [
                'date' => $date,
                'clock_in' => $clockIn?->created_at,
                'clock_out' => $clockOut?->created_at,
                'break_min' => intdiv($breakSec, 60),
                'work_hours' => intdiv($workSec, 3600),
                'work_mins' => intdiv($workSec % 3600, 60),
                'is_weekend' => $date->isWeekend(),
            ];
        }

        //　サマリー
        $totalSec = array_sum(array_map(fn($d) => ($d['work_hours'] * 3600 + $d['work_mins'] * 60), $dailyReport));
        $workDays = collect($dailyReport)->filter(fn($d) => $d['clock_in'])->count();

        $summary = [
            'total_hours' => intdiv($totalSec, 3600),
            'work_days' => $workDays,
            'avg_hours' => $workDays > 0 ? round(intdiv($totalSec, 3600) / $workDays, 1) : 0,
        ];

        return view('reports.monthly', [
            'targetMonth' => $targetMonth,
            'dailyReport' => $dailyReport,
            'summary' => $summary,
            'prevMonth' => $targetMonth->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $targetMonth->copy()->addMonth()->format('Y-m'),
        ]);
    }
}
