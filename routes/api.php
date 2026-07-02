<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NoticeApiController;
use App\Http\Controllers\Api\DisplayController;

// Public (display board has no login)
Route::post('/login', [AuthController::class, 'login']);
Route::get('/display', [DisplayController::class, 'index']);
Route::get('/class-updates', [\App\Http\Controllers\Api\DisplayController::class, 'classUpdates']);
Route::post('/notices/{notice}/view', [NoticeApiController::class, 'logView']);

// Protected (mobile app — needs token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notices', [NoticeApiController::class, 'index']);
    Route::get('/notices/{notice}', [NoticeApiController::class, 'show']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/my-notifications', [\App\Http\Controllers\Api\NoticeApiController::class, 'myNotifications']);
    Route::post('/my-notifications/seen', [\App\Http\Controllers\Api\NoticeApiController::class, 'markNotificationsSeen']);
    Route::post('/cr/notice', [\App\Http\Controllers\Api\NoticeApiController::class, 'crStore']);
    Route::get('/teachers', [\App\Http\Controllers\Api\NoticeApiController::class, 'teachers']);
    Route::post('/notices/{notice}/reply', [\App\Http\Controllers\Api\NoticeApiController::class, 'replyToNotice']);
});