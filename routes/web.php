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
});