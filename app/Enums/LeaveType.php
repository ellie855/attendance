<?php
namespace App\Enums;
 
enum LeaveType: string
{
    case Paid = 'paid';
    case Absence = 'absence';
    case HalfDay = 'half_day';
    case Special = 'special';

    public function label(): string
    {
        return match ($this) {
            self::Paid => '有休休暇',
            self::Absence => '欠勤',
            self::HalfDay => '半休',
            self::Special => '特別休暇',
        };
    }
}