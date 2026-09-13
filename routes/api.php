<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// 認証不要 (ログインでトークン発行)
Route::post('/login', [AuthController::class, 'login']);

// 認証必須 (Sanctumトークン)
Route::middleware('auth:sanctum')->group(function () {

    // ログアウト
    Route::post('/logout', [AuthController::class, 'logout']);

    // 現在のユーザー情報
    Route::get('/user', fn(Request $request) => $request->user());

    // 勤怠
    Route::get('/attendances', [AttendanceController::class, 'index']);
    Route::post('/attendances/clock-in',    [AttendanceController::class, 'clockIn']);
    Route::post('/attendances/clock-out',   [AttendanceController::class, 'clockOut']);
    Route::post('/attendances/break-start', [AttendanceController::class, 'breakStart']);
    Route::post('/attendances/break-end',   [AttendanceController::class, 'breakEnd']);

    // サマリー
    Route::get('/summary', [AttendanceController::class, 'summary']);
});
