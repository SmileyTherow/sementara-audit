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
        foreach ([GrowthMonitoring::class, Harvest::class, HarvestAvailability::class] as $model) {
            Gate::policy($model, DomainPolicy::class);
        }
        Gate::define('manage-users', fn ($u) => $u->is_active && $u->role === 'super_admin');
        Gate::define('manage-settings', fn ($u) => $u->is_active && in_array($u->role, ['super_admin', 'admin_koperasi']));
        View::composer('*', function ($view) {
            $view->with('siteSettings', Setting::pluck('value', 'key'));
        });
    }
}
