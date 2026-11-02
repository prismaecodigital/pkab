<?php

namespace App\Providers;

use App\Models\StatusHistory;
use App\Observers\StatusHistoryActionObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

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
        StatusHistory::observe(StatusHistoryActionObserver::class);

        Event::listen(Registered::class, SendEmailVerificationNotification::class);
    }
}
