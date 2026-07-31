<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_correction_requests', function (Blueprint $table) {
            // 申請種別: 'modify'(既存修正) or  'add'(打刻追加)
            $table->string('type')->default('modify')->after('user_id');

            // add 時のみ使う　: 打刻種類（'clock_in' / 'clock_out' / 'break_start' / 'break_end')
            $table->string('clock_type')->nullable()->after('type');

            // add タイプは既存打刻を指定しないので nullableに変更
            $table->foreignId('target_attendance_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('attendance_correction_requests', function (Blueprint $table) {
            $table->dropColumn(['type', 'clock_type']);
            $table->foreignId('target_attendance_id')->nullable(false)->change();
        });
    }
};
