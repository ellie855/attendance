<?php

namespace App\Enums;

enum CorrectionRequestType: string
{
    case Modify = 'modify';
    case Add    = 'add';

    public function label(): string
    {
        return match ($this) {
            self::Modify => '既存修正',
            self::Add    => '打刻追加',
        };
    }
}
