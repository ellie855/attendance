<?php

namespace App\Services;

use App\Enums\AttendanceType;
use Carbon\Carbon;

class AttendanceSummaryService
{
    /**
     * 打刻レコード一覧から、休憩を差し引いた勤務秒数の合計を返す
     * （出勤～退勤の区間ごとに、その間の休憩時間を引いて集計）
     */
    public function calculateWorkSeconds(iterable $records): int
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

    /**
     * 打刻レコード + 対象月 → 日次レポート配列を返す
     * (月次画面 / CSV 両方から使う)
     */
    public function buildDailyReport(iterable $attendances, Carbon $targetMonth): array
    {
        $collection = collect($attendances);
        $daysInMonth = $targetMonth->daysInMonth;
        $dailyReport = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $targetMonth->copy()->day($day);
            $dayAttendances = $collection->filter(fn($a) => $a->created_at->day === $day);

            $clockIn = $dayAttendances->firstWhere('type', AttendanceType::ClockIn);
            $clockOut = $dayAttendances->where('type', AttendanceType::ClockOut)->last();

            // 休憩時間の合計(秒)
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

            // 勤務時間（秒）
            $workSec = 0;
            if ($clockIn && $clockOut) {
                $workSec = $clockIn->created_at->diffInSeconds($clockOut->created_at) - $breakSec;
            }

            $dailyReport[] = [
                'date'        => $date,
                'clock_in'    => $clockIn?->created_at,
                'clock_out'   => $clockOut?->created_at,
                'break_min'   => intdiv($breakSec, 60),
                'work_hours'  => intdiv($workSec, 3600),
                'work_mins'   => intdiv($workSec % 3600, 60),
                'is_weekend'  => $date->isWeekend(),
            ];
        }

        return $dailyReport;
    }
}