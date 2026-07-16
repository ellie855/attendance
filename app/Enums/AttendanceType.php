<?php

namespace App\Enums;

enum AttendanceType: string
{
    case ClockIn    = 'clock_in';
    case ClockOut   = 'clock_out';
    case BreakStart = 'break_start';
    case BreakEnd   = 'break_end';

    public function label(): string
    {
        return match($this) {
            self::ClockIn    => '出勤',
            self::ClockOut   => '退勤',
            self::BreakStart => '休憩開始',
            self::BreakEnd   => '休憩終了',
        };
    }
}