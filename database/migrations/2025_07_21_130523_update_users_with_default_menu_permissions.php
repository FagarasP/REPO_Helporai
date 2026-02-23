<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing users who don't have menu_permissions set
        $users = User::whereNull('menu_permissions')->orWhere('menu_permissions', '[]')->get();

        foreach ($users as $user) {
            $defaultPermissions = $this->getDefaultMenuPermissions($user->role);
            $user->menu_permissions = $defaultPermissions;
            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally clear menu_permissions if needed
        // User::query()->update(['menu_permissions' => null]);
    }

    /**
     * Get default menu permissions based on user role
     */
    private function getDefaultMenuPermissions(string $role): array
    {
        switch ($role) {
            case 'super_admin':
                return []; // Super admin doesn't need specific permissions
            case 'admin': // Convert old admin role to company
            case 'company':
                return ['company.dashboard'];
            case 'freelancer':
                return ['freelancer.dashboard'];
            case 'general_user': // Convert old general_user to other
            case 'employee': // Convert old employee to other
            case 'other':
            default:
                return ['other.dashboard'];
        }
    }
};
