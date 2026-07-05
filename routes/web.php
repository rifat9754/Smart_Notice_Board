<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
    ->middleware(['auth', 'preventback'])->name('home');

Route::middleware(['auth', 'preventback', 'role:super_admin,teacher'])->group(function () {
    Route::resource('notices', \App\Http\Controllers\NoticeController::class);
});

Route::get('/board/{id}', function ($id) {
    return view('board', ['boardId' => $id]);
});

Route::get('/public-notices', [\App\Http\Controllers\PublicNoticeController::class, 'index'])->name('public.notices');
Route::get('/public-notices/{notice}', [\App\Http\Controllers\PublicNoticeController::class, 'show'])->name('public.show');

Route::middleware(['auth', 'preventback', 'role:super_admin,teacher'])->group(function () {
    Route::get('/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/my-notifications', [\App\Http\Controllers\TeacherNotificationController::class, 'index'])->name('teacher.notifications');
    Route::post('/my-notifications/{notice}/reply', [\App\Http\Controllers\TeacherNotificationController::class, 'reply'])->name('teacher.reply');
});

Route::middleware(['auth', 'preventback', 'role:super_admin'])->group(function () {
    Route::get('/audit', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit.index');

    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [\App\Http\Controllers\UserController::class, 'create'])->name('users.create');
    Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
    Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
});

Route::middleware(['auth', 'preventback', 'role:super_admin'])->group(function () {
    Route::get('/approvals', [\App\Http\Controllers\ApprovalController::class, 'index'])->name('approvals.index');
    Route::post('/approvals/{user}/approve', [\App\Http\Controllers\ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('/approvals/{user}/reject', [\App\Http\Controllers\ApprovalController::class, 'reject'])->name('approvals.reject');
    Route::post('/approvals/{user}/deactivate', [\App\Http\Controllers\ApprovalController::class, 'deactivate'])->name('approvals.deactivate');
    Route::post('/approvals/{user}/activate', [\App\Http\Controllers\ApprovalController::class, 'activate'])->name('approvals.activate');
    Route::post('/approvals/{user}/make-cr', [\App\Http\Controllers\ApprovalController::class, 'makeCr'])->name('approvals.makeCr');
    Route::post('/approvals/{user}/remove-cr', [\App\Http\Controllers\ApprovalController::class, 'removeCr'])->name('approvals.removeCr');
});

Route::middleware(['auth', 'preventback', 'role:student'])->group(function () {
    Route::get('/feed', [\App\Http\Controllers\StudentController::class, 'feed'])->name('student.feed');
    Route::get('/feed/{notice}', [\App\Http\Controllers\StudentController::class, 'show'])->name('student.show');
});

Route::middleware(['auth', 'preventback', 'role:cr'])->group(function () {
    Route::get('/cr', [\App\Http\Controllers\CrController::class, 'index'])->name('cr.index');
    Route::get('/cr/create', [\App\Http\Controllers\CrController::class, 'create'])->name('cr.create');
    Route::post('/cr', [\App\Http\Controllers\CrController::class, 'store'])->name('cr.store');
    Route::delete('/cr/{notice}', [\App\Http\Controllers\CrController::class, 'destroy'])->name('cr.destroy');
});

Route::middleware(['auth', 'preventback'])->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'preventback', 'role:student,cr'])->group(function () {
    Route::get('/feed', [\App\Http\Controllers\StudentController::class, 'feed'])->name('student.feed');
    Route::get('/feed/{notice}', [\App\Http\Controllers\StudentController::class, 'show'])->name('student.show');
});

Route::middleware(['auth', 'preventback', 'role:super_admin,teacher'])->group(function () {
    Route::get('/events', [\App\Http\Controllers\EventController::class, 'index'])->name('events.index');
    Route::post('/events', [\App\Http\Controllers\EventController::class, 'store'])->name('events.store');
    Route::delete('/events/{event}', [\App\Http\Controllers\EventController::class, 'destroy'])->name('events.destroy');
});