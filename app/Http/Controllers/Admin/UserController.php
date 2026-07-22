<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

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
}
