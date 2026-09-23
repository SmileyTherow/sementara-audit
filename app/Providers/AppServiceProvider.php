<?php

namespace App\Providers;

use App\Models\GrowthMonitoring;
use App\Models\Harvest;
use App\Models\HarvestAvailability;
use App\Models\Setting;
use App\Policies\DomainPolicy;
use App\Support\Resources;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
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
        foreach (Resources::all() as [$model]) {
            Gate::policy($model, DomainPolicy::class);
        }

        foreach ([
            GrowthMonitoring::class,
            Harvest::class,
            HarvestAvailability::class,
        ] as $model) {
            Gate::policy($model, DomainPolicy::class);
        }

        Gate::define('manage-users', function ($user): bool {
            return $user->is_active && $user->isSuperAdmin();
        });

        Gate::define('manage-settings', function ($user): bool {
            return $user->is_active && $user->isSuperAdmin();
        });

        View::composer('*', function ($view): void {
            $view->with(
                'siteSettings',
                Setting::query()->pluck('value', 'key')
            );
        });
    }
}