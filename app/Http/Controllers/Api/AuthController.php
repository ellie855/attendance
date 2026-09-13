<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * ログイン: email/password を受け取り、認証成功したら Sanctum トークンを返す
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['認証情報が正しくありません'],
            ]);
        }

        // Sanctum トークン発行
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user'  => $user->only(['id', 'name', 'email', 'role']),
            'token' => $token,
        ]);
    }

    /**
     * ログアウト: 現在のトークンを削除
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'ログアウトしました']);
    }
}
