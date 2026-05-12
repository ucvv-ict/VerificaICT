<?php

namespace App\Providers;

use App\Models\EntitySecurityTask;
use App\Models\SecurityCheck;
use App\Models\SecurityTask;
use App\Observers\EntitySecurityTaskObserver;
use App\Observers\SecurityCheckObserver;
use App\Observers\SecurityTaskObserver;
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
        EntitySecurityTask::observe(EntitySecurityTaskObserver::class);
        SecurityCheck::observe(SecurityCheckObserver::class);
        SecurityTask::observe(SecurityTaskObserver::class);
    }
}
