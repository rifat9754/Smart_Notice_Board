<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use App\Models\Notice;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
public function boot(): void
{
    if (config('app.env') === 'production') {
        URL::forceScheme('https');
    }
    Gate::define('is-student', fn($user) => $user->role === 'student');

    Gate::define('is-cr', fn($user) => $user->role === 'cr');

    View::composer('*', function ($view) {
    $count = 0;
    if (Auth::check() && in_array(Auth::user()->role, ['teacher', 'super_admin'])) {
        $count = Notice::where('notified_teacher_id', Auth::id())
            ->where('notified_seen', false)
            ->count();
    }
    $view->with('unseenNotifCount', $count);
});

}
}
