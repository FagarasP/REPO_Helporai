<?php

namespace App\Policies;

use App\Models\User;

class SettingsPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any settings.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin']);
    }

    /**
     * Determine whether the user can view the settings page.
     */
    public function view(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin']);
    }
}
