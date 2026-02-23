<?php

namespace App\Services;

use App\Models\User;

class CommunicationAuthorizationService
{
    public function canUsersCommunicate(User $left, User $right): bool
    {
        if ($left->id === $right->id) {
            return false;
        }

        if ($left->isAdmin() || $right->isAdmin()) {
            return true;
        }

        if ($left->company_id && $left->company_id === $right->company_id) {
            return true;
        }

        $leftTeamIds = $left->teams()->pluck('teams.id');
        if ($leftTeamIds->isNotEmpty() && $right->teams()->whereIn('teams.id', $leftTeamIds)->exists()) {
            return true;
        }

        if ($left->role === 'freelancer') {
            return $this->freelancerRelatedToCompany($left, $right);
        }

        if ($right->role === 'freelancer') {
            return $this->freelancerRelatedToCompany($right, $left);
        }

        return false;
    }

    private function freelancerRelatedToCompany(User $freelancer, User $otherUser): bool
    {
        if (! $otherUser->company_id) {
            return false;
        }

        return $freelancer->projectApplications()
            ->where('status', '!=', 'rejected')
            ->whereHas('project', fn ($query) => $query->where('company_id', $otherUser->company_id))
            ->exists();
    }
}
