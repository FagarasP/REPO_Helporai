<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'date',
        'description',
        'status',
        'total_debit',
        'total_credit',
        'company_id',
        'created_by',
        'reversed_by',
        'posted_at',
        'reversed_at',
        'reversal_reason',
    ];

    protected $casts = [
        'date' => 'date',
        'posted_at' => 'datetime',
        'reversed_at' => 'datetime',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
    ];

    protected $appends = ['is_balanced'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function journalEntryLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reversedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reversed_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class)->orderBy('line_order');
    }

    public function getIsBalancedAttribute(): bool
    {
        return abs($this->total_debit - $this->total_credit) < 0.01;
    }

    public function calculateTotals(): void
    {
        $this->total_debit = $this->lines()->sum('debit');
        $this->total_credit = $this->lines()->sum('credit');
    }

    public function canBePosted(): bool
    {
        return $this->status === 'draft' &&
               $this->is_balanced &&
               $this->lines()->count() >= 2 &&
               $this->total_debit > 0;
    }

    public function canBeReversed(): bool
    {
        return $this->status === 'posted';
    }

    public static function generateReference(): string
    {
        $lastEntry = static::orderBy('created_at', 'desc')->first();
        $number = $lastEntry ? (int) substr($lastEntry->reference, 2) + 1 : 1;

        return 'JE'.str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
