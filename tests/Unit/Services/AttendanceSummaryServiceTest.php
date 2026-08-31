<?php

use App\Enums\AttendanceType;
use App\Services\AttendanceSummaryService;
use Carbon\Carbon;

/**
 * 打刻レコードのモック配列を作るヘルパー
 * DB に触らず、new Attendance でオンメモリのモデルを作る
 */
function makeAttendance(AttendanceType $type, string $datetime): object
{
    return(object) [
        'type' => $type,
        'created_at' => Carbon::parse($datetime),
    ];
}

describe('AttendanceSummaryService', function () {
    beforeEach(function () {
        $this->service = new AttendanceSummaryService();
    });

    describe('calculateWorkSeconds', function () {
        it('動作確認: とりあえず空配列で 0 を返す', function () {
            $result = $this->service->calculateWorkSeconds([]);
            expect($result)->toBe(0);
        });

        it('基本パターン: 9:00 出勤 → 17:00 退勤 で 8時間', function () {
            $records = [
                makeAttendance(AttendanceType::ClockIn,  '2026-08-24 09:00:00'),
                makeAttendance(AttendanceType::ClockOut, '2026-08-24 17:00:00'),
            ];

            $result = $this->service->calculateWorkSeconds($records);

            expect($result)->toBe(8 * 3600);
        });

        it('休憩差引: 12:00-13:00 の 1時間休憩は引かれる', function () {
            $records = [
                makeAttendance(AttendanceType::ClockIn,    '2026-08-24 09:00:00'),
                makeAttendance(AttendanceType::BreakStart, '2026-08-24 12:00:00'),
                makeAttendance(AttendanceType::BreakEnd,   '2026-08-24 13:00:00'),
                makeAttendance(AttendanceType::ClockOut,   '2026-08-24 18:00:00'),
            ];

            $result = $this->service->calculateWorkSeconds($records);

            // 09:00-18:00 = 9時間から 1時間休憩を差し引いて 8時間
            expect($result)->toBe(8 * 3600);
        });

        it('進行中(退勤なし)は 0 を返す', function () {
            $records = [
                makeAttendance(AttendanceType::ClockIn, '2026-08-24 09:00:00'),
            ];

            $result = $this->service->calculateWorkSeconds($records);

            // 退勤打刻が無いので、集計対象にならず 0
            expect($result)->toBe(0);
        });
    });

    describe('buildDailyReport', function () {
        it('空データ: 8月なら31日分の配列を返す (中身は全部 null / 0)', function () {
            $targetMonth = Carbon::parse('2026-08-01');
            $result = $this->service->buildDailyReport(collect([]), $targetMonth);

            expect($result)->toBeArray();
            expect(count($result))->toBe(31);
            expect($result[0]['clock_in'])->toBeNull();
            expect($result[0]['work_hours'])->toBe(0);
        });

        it('1日分の打刻: 該当日は集計され、他日は 0', function () {
            // 2026-08-24(月)に9:00出勤 → 18:00退勤、12:00-13:00休憩
            $records = collect([
                makeAttendance(AttendanceType::ClockIn,     '2026-08-24 09:00:00'),
                makeAttendance(AttendanceType::BreakStart,  '2026-08-24 12:00:00'),
                makeAttendance(AttendanceType::BreakEnd,    '2026-08-24 13:00:00'),
                makeAttendance(AttendanceType::ClockOut,    '2026-08-24 18:00:00'),
            ]);
            $targetMonth = Carbon::parse('2026-08-01');

            $result = $this->service->buildDailyReport($records, $targetMonth);

            // 8/24は配列の [23]に格納される (0-indexedで24日目)
            $day24 = $result[23];
            expect($day24['work_hours'])->toBe(8);
            expect($day24['work_mins'])->toBe(0);
            expect($day24['break_min'])->toBe(60);
            expect($day24['is_weekend'])->toBeFalse(); // 月曜日

            // 当日(例: 8/1)は打刻なし
            expect($result[0]['clock_in'])->toBeNull();
            expect($result[0]['work_hours'])->toBe(0);
        });

        it('複数日打刻: 各日が独立して集計される', function () {
            $records = collect([
                // 8/24(月): 09:00 - 17:00 = 8時間
                makeAttendance(AttendanceType::ClockIn,   '2026-08-24 09:00:00'),
                makeAttendance(AttendanceType::ClockOut,  '2026-08-24 17:00:00'),
                // 8/25(火): 10:00 - 15:30 = 5時間30分
                makeAttendance(AttendanceType::ClockIn,   '2026-08-25 10:00:00'),
                makeAttendance(AttendanceType::ClockOut,  '2026-08-25 15:30:00'),
            ]);
            $targetMonth = Carbon::parse('2026-08-01');

            $result = $this->service->buildDailyReport($records, $targetMonth);

            // 08/24
            expect($result[23]['work_hours'])->toBe(8);
            expect($result[23]['work_mins'])->toBe(0);

            // 08/25
            expect($result[24]['work_hours'])->toBe(5);
            expect($result[24]['work_mins'])->toBe(30);
        });
    });
});