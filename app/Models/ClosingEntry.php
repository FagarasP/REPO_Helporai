<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClosingEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'closing_date',
        'period_start',
        'period_end',
        'period_description',
        'total_revenue_closed',
        'total_expense_closed',
        'net_income',
        'retained_earnings_account_id',
        'journal_entry_reference',
        'status',
        'closed_accounts',
        'created_by',
        'posted_at',
    ];

    protected $casts = [
        'closing_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'total_revenue_closed' => 'decimal:2',
        'total_expense_closed' => 'decimal:2',
        'net_income' => 'decimal:2',
        'closed_accounts' => 'array',
        'posted_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function retainedEarningsAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'retained_earnings_account_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_reference', 'reference');
    }
}
