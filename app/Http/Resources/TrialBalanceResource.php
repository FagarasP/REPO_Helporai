<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrialBalanceResource extends JsonResource
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
            'company_id' => $this->company_id,
            'as_of_date' => $this->as_of_date->format('Y-m-d'),
            'total_debits' => $this->total_debits,
            'total_credits' => $this->total_credits,
            'is_balanced' => $this->is_balanced,
            'balances' => $this->balances,
            'generated_by' => $this->generatedBy ? [
                'id' => $this->generatedBy->id,
                'name' => $this->generatedBy->name,
            ] : [
                'id' => null,
                'name' => 'Unknown User',
            ],
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
