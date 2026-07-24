<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DailyNoteController;
use App\Http\Controllers\AttendanceRequestController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\AttendanceRequestController as AdminAttendanceRequestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return auth()->check() ? redirect('/attendances') : redirect('/login');
});

Route::get('/dashboard', function () {
    return redirect('/attendances');
})->middleware(['auth', 'verified'])->name('dashboard');

// プロフィール (Breeze 標準、ログイン必須)
Route::middleware('auth')->group(function () {
    Route::get('/profile',     [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',   [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',  [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 掲示板 (ログイン不要、誰でも閲覧)
    Route::get('/posts',                  [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create',           [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts',                 [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit',      [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}',           [PostController::class, 'update'])->name('posts.update');
    Route::get('/posts/{post}',           [PostController::class, 'show'])->name('posts.show');
    Route::delete('/posts/{post}',        [PostController::class, 'destroy'])->name('posts.destroy');

// 勤怠 (ログイン必須)
Route::middleware('auth')->group(function () {
    Route::get('/attendances',                    [AttendanceController::class, 'index'])->name('attendances.index');
    Route::post('/attendances/clock-in',          [AttendanceController::class, 'clockIn'])->name('attendances.clock-in');
    Route::post('/attendances/clock-out',         [AttendanceController::class, 'clockOut'])->name('attendances.clock-out');
    Route::post('/attendances/break-start',       [AttendanceController::class, 'breakStart'])->name('attendances.break-start');
    Route::post('/attendances/break-end',         [AttendanceController::class, 'breakEnd'])->name('attendances.break-end');
    Route::get('/attendances/confirm-in',         [AttendanceController::class, 'confirmClockIn'])->name('attendances.confirm-in');
    Route::get('/attendances/confirm-out',        [AttendanceController::class, 'confirmClockOut'])->name('attendances.confirm-out');
    Route::get('/attendances/{attendance}/edit',  [AttendanceController::class, 'edit'])->name('attendances.edit');
    Route::put('/attendances/{attendance}',       [AttendanceController::class, 'update'])->name('attendances.update');
    Route::delete('/attendances/{attendance}',    [AttendanceController::class, 'destroy'])->name('attendances.destroy');
    Route::post('/daily-notes',                   [DailyNoteController::class, 'store'])->name('daily-notes.store');
    Route::get('/attendance-requests',            [AttendanceRequestController::class, 'index'])->name('attendance-requests.index');
    Route::get('/attendance-requests/create',     [AttendanceRequestController::class, 'create'])->name('attendance-requests.create');
    Route::post('/attendance-requests',           [AttendanceRequestController::class, 'store'])->name('attendance-requests.store');
    Route::get('/attendance-requests/{attendanceRequest}/edit',  [AttendanceRequestController::class, 'edit'])->name('attendance-requests.edit');
    Route::get('/attendance-requests/{attendanceRequest}',       [AttendanceRequestController::class, 'show'])->name('attendance-requests.show');
    Route::put('/attendance-requests/{attendanceRequest}',       [AttendanceRequestController::class, 'update'])->name('attendance-requests.update');
    Route::delete('/attendance-requests/{attendanceRequest}',    [AttendanceRequestController::class, 'destroy'])->name('attendance-requests.destroy');
    Route::get('/notifications',                                 [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read',                        [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::get('/reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
});


// 管理者専用
Route::middleware(['auth', \App\Http\Middleware\EnsureUserIsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users',                                             [AdminUserController::class, 'index'])->name('users.index');
    Route::put('/users/{user}/role',                                 [AdminUserController::class, 'updateRole'])->name('users.update-role');
    Route::delete('/users/{user}',                                   [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::get('/attendance-requests',                               [AdminAttendanceRequestController::class, 'index'])->name('attendance-requests.index');
    Route::put('/attendance-requests/{attendanceRequest}/approve',   [AdminAttendanceRequestController::class, 'approve'])->name('attendance-requests.approve');
    Route::put('/attendance-requests/{attendanceRequest}/reject',    [AdminAttendanceRequestController::class, 'reject'])->name('attendance-requests.reject');
});





require __DIR__.'/auth.php';