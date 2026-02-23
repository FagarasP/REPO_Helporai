<?php

namespace App\Policies;

use App\Models\TrialBalanceSnapshot;
use App\Models\User;

class TrialBalanceSnapshotPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TrialBalanceSnapshot $trialBalanceSnapshot): bool
    {
        if ($user->hasRole('suer_admin')) {
            return true;
        }

        // Users can only view snapshots from their own company
        return $user->company_id === $snapshot->company_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'accountant']);
    }

    public function export(User $user, TrialBalanceSnapshot $snapshot): bool
    {
        return $this->view($user, $snapshot);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TrialBalanceSnapshot $trialBalanceSnapshot): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TrialBalanceSnapshot $trialBalanceSnapshot): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TrialBalanceSnapshot $trialBalanceSnapshot): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TrialBalanceSnapshot $trialBalanceSnapshot): bool
    {
        return false;
    }
}
