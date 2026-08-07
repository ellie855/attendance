<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id')->get();
        
        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:user,admin',
        ]);

        $user->update(['role' => $validated['role']]);

        return redirect()->route('admin.users.index')
            ->with('success', "{$user->name}の役割を変更しました");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', '自分自身は削除できません');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'ユーザーを削除しました');
    }

    public function import(Request $request)
    {
        $startTime = microtime(true);
        set_time_limit(300); 

        // ファイル検証
        $request->validate([
            'csv' => 'required|file|mimes:csv,txt|max:2048', // 2MB　まで
        ]);

        // 2. CSV読み込み　→　全行を配列に（まだDB触らない）
        $file = $request->file('csv');
        $fp = fopen($file->getRealPath(), 'r');

        // BOMをスキップ〈戦闘3バイト分読み捨て）
        $bom = fread($fp, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($fp); // BOMじゃなかったら先頭に戻す
        }

        // ヘッダー行を読み捨て
        fgetcsv($fp);

        $rows = [];
        $errors = [];
        $rowNum = 1; //ヘッダーが一行目なので、データは二行目から
        while (($row = fgetcsv($fp)) !== false) {
            $rowNum++;
            $row = array_map('trim', $row);
            // 空行はスキップ（エラー扱いにしない）
            if ($row === [''] || $row === [null] || (count($row) === 1 && $row[0] === '')) {
                continue;
            }
            // 列数チェック(name, email, password, roleの４列)
            if (count($row) < 4) {
                $errors[] ="行{$rowNum}: 列数が足りません";
                continue;
            }
            $rows[] = ['num' => $rowNum, 'data' => $row];
        }
        fclose($fp);

        // 3. フォーマットバリデーション(DB叩かない)
        $validRows = [];
        foreach ($rows as $r) {
            [$name, $email, $password, $role] = $r['data'];
            $validator = Validator::make(compact('name', 'email', 'password', 'role'), [
                'name'      => 'required|string|max:255',
                'email'     => 'required|email|max:255',
                'password'  => ['required', 'string', Password::defaults()],
                'role'      => 'required|in:user,admin',
            ]);
            if ($validator->fails()) {
                $errors[] = "行{$r['num']}({$email}): " . $validator->errors()->first();
                continue;
            }
            $validRows[] = compact('name', 'email', 'password', 'role') + ['num' => $r['num']];
        }

        // 4. 既存メールを一回で全部取得（N+1問題を回避）+ 小文字統一
        $emails = array_map('strtolower', array_column($validRows, 'email'));
        // ↓キーに email を入れて0(1) 検索できるようにする
        $existingEmails = array_flip(
            array_map('strtolower', User::whereIn('email', $emails)->pluck('email')->toArray())
        );

        // 5. 重複除外　+　CSV内での重複もチェック(大文字小文字を区別せず)
        $newRows = [];
        $seenEmails = [];   // これもキー =email の形式で使う
        foreach ($validRows as $r) {
            $emailLower = strtolower($r['email']);
            if (isset( $existingEmails[$emailLower]) || isset($seenEmails[$emailLower])) {
                $errors[] = "行{$r['num']}({$r['email']}): このメールアドレスは既に使用されています";
                continue;
            }
            $seenEmails[$emailLower] = true;
            $r['email'] = $emailLower;   // 保存も小文字で統一
            $newRows[] = $r;
        }
        
        // 6. パスワードハッシュ化　+　insert 用データ整形
        $now = now();
        $insertData = array_map(fn($r) => [
                'name'       => $r['name'],
                'email'      => $r['email'],
                'password'   => Hash::make($r['password']),
                'role'       => $r['role'],
                'created_at' => $now,
                'updated_at' => $now,
        ], $newRows);

        // 7. 500件づつバルクインサート（トランザクション）
        DB::transaction(function () use ($insertData) {
            foreach (array_chunk($insertData, 500) as $chunk) {
                User::insert($chunk);
            }
        });

        $imported = count($insertData);
        $elapsed = round(microtime(true) - $startTime, 2);
        Log::info("CSV Import: {$imported}件成功, {$elapsed}秒");

        $message = "{$imported}件のユーザーを登録しました ({$elapsed}秒)";
        if (!empty($errors)) {
            $message .= '(' . count($errors) . '件スキップ)';
        }

        // 0件登録なら　error 扱い、それ以外は success
        $flashkey = $imported === 0 ? 'error' : 'success';

        return redirect()->route('admin.users.index')
            ->with('success', $message)
            ->with('import_errors', $errors);

        return redirect()->route('admin.users.index')
            ->with('success', $message);
    }
}
