<?php

use App\Portals\AdminPortal;
use App\Portals\CompanyPortal;
use App\Portals\FreelancerPortal;
use Illuminate\Support\Facades\Route;

$portals = [
    app(AdminPortal::class),
    app(CompanyPortal::class),
    app(FreelancerPortal::class),
];

foreach ($portals as $portal) {
    Route::middleware(['auth', 'verified', 'portal:'.$portal->key()])
        ->prefix($portal->key())
        ->as($portal->key().'.')
        ->group(function () use ($portal) {
            foreach ($portal->modules() as $module) {
                $routesFile = $module['base_path'].'/routes.php';

                if (file_exists($routesFile)) {
                    Route::middleware(['permission:'.($module['route_permission'] ?? $module['id'].'.read')])
                        ->group($routesFile);
                }
            }
        });
}
