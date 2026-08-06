<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;

test('未ログインで勤怠画面にアクセスするとログイン画面にリダイレクトされる', function () {
    $response = $this->get('/attendances');

    $response->assertRedirect('/login');
});

test('ログイン済みユーザーは勤怠画面を開ける', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/attendances');

    $response->assertOk();

});

test('ログイン済みユーザーが出勤打刻するとDBに1件記録される', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/attendances/clock-in');

    $response->assertRedirect('/attendances');

    $this->assertDatabaseHas('attendances', [
        'user_id' => $user->id,
        'type'    => 'clock_in',
    ]);
});

test('ログイン済みユーザーが退勤打刻するとDBに1件記録される', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/attendances/clock-out');

    $response->assertRedirect('/attendances');

    $this->assertDatabaseHas('attendances', [
        'user_id' => $user->id,
        'type'    => 'clock_out',
    ]);
});

test('管理者がCSVをアップロードするとユーザーが登録される', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    // 疑似CSVファイルを作る
    $csvContent = "name, email, password, role\n"
                . "テスト太郎,csv1@example.com,password123,user\n"
                . "テスト花子,csv2@example.com,password123,user\n";

    $file = UploadedFile::fake()->createWithContent('users.csv', $csvContent);

    $response = $this->actingAs($admin)->post('/admin/users/import', [
        'csv' => $file,
    ]);

    $response->assertRedirect('/admin/users');

    $this->assertDatabaseHas('users', ['email' => 'csv1@example.com']);
    $this->assertDatabaseHas('users', ['email' => 'csv2@example.com']);    
});

test('他人の修正申請は編集画面を開けない', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    $request = \App\Models\AttendanceCorrectionRequest::create([
        'user_id'     => $owner->id,
        'type'        => 'add',
        'clock_type'  => 'clock_in',
        'new_time'    => now(),
        'reason'      => 'テスト',
    ]);

    $response = $this->actingAs($other)
        ->get("/attendance-requests/{$request->id}/edit");

    $response->assertForbidden();
});