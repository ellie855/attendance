<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class ImportUserFromCsvJob implements ShouldQueue
{
    use Queueable;

    // 一時保存された CSVファイルのパスを受け取る
    public function __construct(public string $filePath)
    {
    }

    public function handle(): void
    {
        $startTime = microtime(true);

        // 一時ファイルを開く
        $fullPath = Storage::path($this->filePath);
        $fp = fopen($fullPath, 'r');

        // BOM スキップ
        $bom = fread($fp, 3);
        if ($bom !=="\xEF\xBB\xBF") {
            rewind($fp);
        }

        // ヘッダー読み捨て
        fgetcsv($fp);

        $rows = [];
        $errors = [];
        $rowNum = 1;
        while (($row = fgetcsv($fp)) !== false) {
            $rowNum++;
            $row = array_map('trim', $row);
            if ($row === [''] || $row === [null] || (count($row) === 1 && $row[0] === '')) {
                continue;
            }
            if (count($row) < 4) {
                $errors[] = "行{$rowNum}: 列数が足りません";
                continue;
            }
            $rows[] = ['num' => $rowNum, 'data' => $row];
        }
    fclose($fp);

    //フォーマットバリデーション
    $validRows = [];
    foreach ($rows as $r) {
        [$name, $email, $password, $role] = $r['data'];
        $validator = Validator::make(compact('name', 'email', 'password', 'role'), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'password' => ['required', 'string', Password::defaults()],
            'role'     => 'required|in:user,admin',
        ]);
        if($validator->fails()) {
            $errors[] = "行{$r['num']}({$email}]: " . $validator->errors()->first();
            continue;
        }
        $validRows[] = compact('name', 'email', 'password', 'role') + ['num' => $r['num']];
    }

    // 既存メール取得(0(1)化)
    $emails = array_map('strtolower', array_column($validRows, 'email'));
    $existingEmails = array_flip(
        array_map('strtolower', User::whereIn('email', $emails)->pluck('email')->toArray())
    );

    // 重複除外
    $newRows = [];
    $seenEmails = [];
    foreach ($validRows as $r) {
        $emailLower = strtolower($r['email']);
        if (isset($existingEmails[$emailLower]) || isset($seenEmails[$emailLower])) {
            $errors[] = "行{$r['num']}({$r['email']}): このメールアドレスは既に使用されています";
            continue;
        }
        $seenEmails[$emailLower] = true;
        $r['email'] = $emailLower;
        $newRows[] = $r;
    }

    // ハッシュ化 + insert 用整形
    $now = now();
    $insertData = array_map(fn($r) => [
        'name'         => $r['name'],
        'email'        => $r['email'],
        'password'     => Hash::make($r['password']),
        'role'         => $r['role'],
        'created_at'   => $now,
        'updated_at'   => $now,
    ], $newRows);

    // バルクインサート
    DB::transaction(function () use ($insertData) {
        foreach (array_chunk($insertData, 500) as $chunk) {
            User::insert($chunk);
        }
    });

    $imported = count($insertData);
    $elapsed = round(microtime(true) - $startTime, 2);

    // 結果をログに出力(通知処理はまた将来)
    Log::info("CSV Import Job 完了: {$imported}件成功, " . count($errors) . "件スキップ, {$elapsed}秒");
    if (!empty($errors)) {
        Log::info("スキップ詳細: " . json_encode($errors, JSON_UNESCAPED_UNICODE));
    }

    //一時ファイル削除
    Storage::delete($this->filePath);
}
}