<?php

namespace App\Providers;

use App\Portals\AdminPortal;
use App\Portals\CompanyPortal;
use App\Portals\FreelancerPortal;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        foreach (['admin', 'company', 'freelancer'] as $portal) {
            Gate::define("portal.{$portal}", fn ($user) => $user->canAccessPortal($portal));
        }
    }

    public function register(): void
    {
        $this->app->singleton(AdminPortal::class);
        $this->app->singleton(CompanyPortal::class);
        $this->app->singleton(FreelancerPortal::class);
    }
}
