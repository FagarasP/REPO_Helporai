<?php

// app/Http/Resources/ClosingEntryResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClosingEntryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'closing_date' => $this->closing_date->format('Y-m-d'),
            'period_start' => $this->period_start->format('Y-m-d'),
            'period_end' => $this->period_end->format('Y-m-d'),
            'period_description' => $this->period_description,
            'total_revenue_closed' => $this->total_revenue_closed,
            'total_expense_closed' => $this->total_expense_closed,
            'net_income' => $this->net_income,
            'status' => $this->status,
            'journal_entry_reference' => $this->journal_entry_reference,
            'retained_earnings_account' => [
                'id' => $this->retainedEarningsAccount->id,
                'account_number' => $this->retainedEarningsAccount->account_number,
                'account_name' => $this->retainedEarningsAccount->account_name,
            ],
            'created_by' => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
            ],
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'posted_at' => $this->posted_at?->format('Y-m-d H:i:s'),
        ];
    }
}
