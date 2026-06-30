<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::middleware(['auth', 'role:super_admin,teacher'])->group(function () {
    Route::resource('notices', \App\Http\Controllers\NoticeController::class);
});

Route::get('/board/{id}', function ($id) {
    return view('board', ['boardId' => $id]);
});

Route::middleware(['auth', 'role:super_admin,teacher'])->group(function () {
    Route::get('/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics.index');
});

Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/audit', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit.index');

    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [\App\Http\Controllers\UserController::class, 'create'])->name('users.create');
    Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
    Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
});

Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/approvals', [\App\Http\Controllers\ApprovalController::class, 'index'])->name('approvals.index');
    Route::post('/approvals/{user}/approve', [\App\Http\Controllers\ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('/approvals/{user}/reject', [\App\Http\Controllers\ApprovalController::class, 'reject'])->name('approvals.reject');
    Route::post('/approvals/{user}/deactivate', [\App\Http\Controllers\ApprovalController::class, 'deactivate'])->name('approvals.deactivate');
    Route::post('/approvals/{user}/activate', [\App\Http\Controllers\ApprovalController::class, 'activate'])->name('approvals.activate');
});

Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/feed', [\App\Http\Controllers\StudentController::class, 'feed'])->name('student.feed');
    Route::get('/feed/{notice}', [\App\Http\Controllers\StudentController::class, 'show'])->name('student.show');
});