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

    public function import (Request $request)
    {
        // ファイル検証
        $request->validate([
            'csv' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $path = $request->file('csv')->store('imports');

        \App\Jobs\ImportUserFromCsvJob::dispatch($path);

        return redirect()->route('admin.users.index')
            ->with('success', 'CSVのインポート処理を開始しました（バックグラウンドで実行中)');
    }

}
