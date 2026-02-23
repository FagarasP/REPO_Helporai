<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChartOfAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_number',
        'account_name',
        'account_type',
        'account_sub_type',
        'description',
        'tax_rate',
        'opening_balance',
        'current_balance',
        'is_active',
        'parent_id',
        'company_id',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:2',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Account types
    const ACCOUNT_TYPES = [
        'asset' => 'Asset',
        'liability' => 'Liability',
        'equity' => 'Equity',
        'revenue' => 'Revenue',
        'expense' => 'Expense',
    ];

    // Account sub-types
    const ACCOUNT_SUB_TYPES = [
        'current_asset' => 'Current Asset',
        'non_current_asset' => 'Non-Current Asset',
        'current_liability' => 'Current Liability',
        'non_current_liability' => 'Non-Current Liability',
        'share_capital' => 'Share Capital',
        'retained_earnings' => 'Retained Earnings',
        'sales_revenue' => 'Sales Revenue',
        'other_income' => 'Other Income',
        'operating_expense' => 'Operating Expense',
        'financial_expense' => 'Financial Expense',
    ];

    // Relationships
    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class, 'account_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('account_type', $type);
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // Accessors & Mutators
    public function getFormattedBalanceAttribute()
    {
        return number_format($this->current_balance, 2);
    }

    public function getAccountTypeDisplayAttribute()
    {
        return self::ACCOUNT_TYPES[$this->account_type] ?? $this->account_type;
    }

    public function getAccountSubTypeDisplayAttribute()
    {
        return self::ACCOUNT_SUB_TYPES[$this->account_sub_type] ?? $this->account_sub_type;
    }

    public function journalEntryLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    // Methods
    public function updateBalance($amount, $type = 'debit')
    {
        if ($type === 'debit') {
            $this->current_balance += $amount;
        } else {
            $this->current_balance -= $amount;
        }
        $this->save();
    }

    public function hasTransactions()
    {
        return $this->journalEntries()->exists();
    }

    public function canBeDeleted()
    {
        return ! $this->hasTransactions() && $this->children()->count() === 0;
    }
}
