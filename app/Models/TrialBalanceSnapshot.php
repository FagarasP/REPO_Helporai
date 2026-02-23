<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrialBalanceSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'as_of_date',
        'balances',
        'total_debits',
        'total_credits',
        'is_balanced',
        'generated_by',
    ];

    protected $casts = [
        'as_of_date' => 'date',
        'balances' => 'array',
        'total_debits' => 'decimal:2',
        'total_credits' => 'decimal:2',
        'is_balanced' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
