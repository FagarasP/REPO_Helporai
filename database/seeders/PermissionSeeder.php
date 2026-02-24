<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Support\Modules\ModuleRegistry;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = app(ModuleRegistry::class)->allPermissions();

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'slug' => $permission,
            ], [
                'name' => $permission,
            ]);
        }
    }
}
