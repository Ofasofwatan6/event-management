<?php

namespace App\Providers;

use App\Models\Event;
use App\Models\User;
use App\Observers\EventHistoryObserver;
use App\Observers\UserHistoryObserver;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        User::observe(UserHistoryObserver::class);
        Event::observe(EventHistoryObserver::class);

        Validator::replacer('required', fn () => 'Wajib Diisi');

        View::composer('*', function ($view) {
            $view->with('appLogoUrl', config('volunteerhub.logo_url'));
            $view->with('profileUrl', ! auth()->check()
                ? route('login')
                : (auth()->user()->role === 'organization'
                    ? route('admin.profile')
                    : route('user.profile.history')));
        });
    }
}
