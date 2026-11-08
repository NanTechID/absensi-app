<?php

namespace App\Providers;

use App\Models\Absensi;
use App\Policies\AbsensiPolicy;
use Illuminate\Support\Facades\Gate;
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
        Gate::policy(Absensi::class, AbsensiPolicy::class);
    }
}
