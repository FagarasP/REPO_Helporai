<?php

namespace App\Support\Modules;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class ModuleRegistry
{
    public function forPortal(string $portal): Collection
    {
        $portalPath = app_path('Modules/'.ucfirst($portal));

        if (! File::isDirectory($portalPath)) {
            return collect();
        }

        return collect(File::directories($portalPath))
            ->map(fn (string $directory) => $directory.'/module.php')
            ->filter(fn (string $manifest) => File::exists($manifest))
            ->map(function (string $manifest) {
                $module = require $manifest;
                $module['base_path'] = dirname($manifest);

                return $module;
            })
            ->filter(fn (array $module) => $this->isEnabled($module))
            ->sortBy(fn (array $module) => Arr::get($module, 'order', 9999))
            ->values();
    }

    public function allPermissions(): array
    {
        return collect(config('modules', []))
            ->keys()
            ->flatMap(fn (string $portal) => $this->forPortal($portal))
            ->flatMap(function (array $module) {
                $permissionsPath = $module['base_path'].'/permissions.php';

                return File::exists($permissionsPath) ? require $permissionsPath : [];
            })
            ->values()
            ->all();
    }

    private function isEnabled(array $module): bool
    {
        $portal = Arr::get($module, 'portal');
        $id = Arr::get($module, 'id');

        return (bool) config("modules.{$portal}.{$id}", Arr::get($module, 'enabled', false));
    }
}
