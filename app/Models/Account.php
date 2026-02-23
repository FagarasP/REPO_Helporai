<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'account_number',
        'account_name',
        'account_type',
        'sub_type',
        'description',
        'is_active',
        'parent_account_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function getBalanceAsOfDate($date)
    {
        return $this->journalEntries()
            ->whereHas('journalEntry', function ($query) use ($date) {
                $query->where('date', '<=', $date)
                    ->where('status', 'posted');
            })
            ->selectRaw('COALESCE(SUM(debit_amount - credit_amount), 0) as balance')
            ->first()
            ->balance ?? 0;
    }

    public function isDebitAccount(): bool
    {
        return in_array($this->account_type, ['Asset', 'Expense']);
    }
}
