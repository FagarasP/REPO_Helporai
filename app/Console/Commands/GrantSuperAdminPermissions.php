<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Console\Command;

class GrantSuperAdminPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:grant-super-admin-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grants all existing permissions to the super_admin user.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $superAdmin = User::where('role', 'super_admin')->first();

        if (! $superAdmin) {
            $superAdmin = User::where('email', 'a@a.com')->first();
            if ($superAdmin) {
                $superAdmin->role = 'super_admin';
                $superAdmin->save();
                $this->info('User a@a.com updated to super_admin role.');
            } else {
                $this->error('No super_admin user found and a@a.com does not exist.');

                return;
            }
        }

        $permissions = Permission::all();

        if ($permissions->isEmpty()) {
            $this->info('No permissions found in the database.');

            return;
        }

        $superAdmin->permissions()->sync($permissions->pluck('id'));

        $this->info('All permissions granted to the super_admin user.');
    }
}
