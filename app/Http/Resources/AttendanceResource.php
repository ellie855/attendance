<?php

namespace App\Http\Resources;

use App\Enums\AttendanceType;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    /**
     * JSON にシリアライズする際の配列表現
     */
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'type'        => $this->type->value,       // enum value("clock_in" 等)
            'type_label'  => $this->typeLabel(),       // 日本語ラベル("出勤" 等)
            'created_at'  => $this->created_at->toIso8601string(),
            'time'        => $this->created_at->format('H:i'),
            'date'        => $this->created_at->format('Y-m-d'),
        ];
    }

    /**
     * type の日本語表示
     */
    private function typeLabel(): string
    {
        return match ($this->type) {
            AttendanceType::ClockIn     => '出勤',
            AttendanceType::ClockOut    => '退勤',
            AttendanceType::BreakStart  => '休憩開始',
            AttendanceType::BreakEnd    => '休憩終了',
        };
    }
}
