<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 休暇申請テーブル作成
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            // 申請者
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // 休暇種類
            $table->string('type');                              //  paid/absence/half_day/special

            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason');
            // 状態
            $table->string('status')->default('pending');        //　pending/approved/rejected
            // 承認した管理者
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('approved_at')->nullable();
            $table->text('admin_comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
