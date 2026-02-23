<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FreelancerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'helper_since' => $this->created_at->format('d-m-Y'),
            'languages' => $this->languages,
            'years_of_experience' => $this->years_of_experience,
            'notes' => '-',
            'activity' => '-',
            'projects_count' => $this->teams->flatMap(function ($team) {
                return $team->project ? [$team->project] : [];
            })->count(),
            'is_active' => $this->is_active,
        ];
    }
}
