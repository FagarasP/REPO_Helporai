<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\TrialBalanceSnapshot;
use Carbon\Carbon;

class TrialBalanceService
{
    public function generateTrialBalance(Company $company, Carbon $asOfDate, int $userId): TrialBalanceSnapshot
    {
        // Get all active accounts for the company
        $accounts = ChartOfAccount::where('company_id', $company->id)
            ->where('is_active', true)
            ->orderBy('account_number')
            ->get();

        $balances = [];
        $totalDebits = 0;
        $totalCredits = 0;

        foreach ($accounts as $account) {
            $balance = $this->calculateAccountBalance($account, $asOfDate);

            if (abs($balance) >= 0.01) { // Only include accounts with meaningful balances
                $accountData = [
                    'account_id' => $account->id,
                    'account_number' => $account->account_number,
                    'account_name' => $account->account_name,
                    'account_type' => $account->account_type,
                    'balance' => $balance,
                    'debit' => 0,
                    'credit' => 0,
                ];

                // Determine if balance should be shown as debit or credit based on account type
                if ($this->isDebitAccount($account->account_type)) {
                    if ($balance >= 0) {
                        $accountData['debit'] = $balance;
                        $totalDebits += $balance;
                    } else {
                        $accountData['credit'] = abs($balance);
                        $totalCredits += abs($balance);
                    }
                } else {
                    if ($balance >= 0) {
                        $accountData['credit'] = $balance;
                        $totalCredits += $balance;
                    } else {
                        $accountData['debit'] = abs($balance);
                        $totalDebits += abs($balance);
                    }
                }

                $balances[] = $accountData;
            }
        }

        return TrialBalanceSnapshot::create([
            'company_id' => $company->id,
            'as_of_date' => $asOfDate,
            'balances' => $balances,
            'total_debits' => $totalDebits,
            'total_credits' => $totalCredits,
            'is_balanced' => abs($totalDebits - $totalCredits) < 0.01,
            'generated_by' => $userId,
        ]);
    }

    private function calculateAccountBalance(ChartOfAccount $account, Carbon $asOfDate): float
    {
        // Calculate balance from journal entry lines
        $result = \DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entry_lines.chart_of_account_id', $account->id)
            ->where('journal_entries.company_id', $account->company_id)
            ->where('journal_entries.date', '<=', $asOfDate)
            ->where('journal_entries.status', 'posted')
            ->selectRaw('SUM(journal_entry_lines.debit - journal_entry_lines.credit) as balance')
            ->first();

        return (float) ($result->balance ?? 0);
    }

    private function isDebitAccount(string $accountType): bool
    {
        return in_array(strtolower($accountType), ['asset', 'expense']);
    }

    public function validateTrialBalance(TrialBalanceSnapshot $snapshot): array
    {
        $issues = [];

        // Check if debits equal credits
        if (abs($snapshot->total_debits - $snapshot->total_credits) > 0.01) {
            $issues[] = [
                'type' => 'balance_mismatch',
                'message' => 'Total debits do not equal total credits',
                'difference' => $snapshot->total_debits - $snapshot->total_credits,
            ];
        }

        // Check for accounts with unusual balances
        foreach ($snapshot->balances as $balance) {
            $account = ChartOfAccount::find($balance['account_id']);

            if ($account) {
                // Asset and Expense accounts should normally have debit balances
                if (in_array(strtolower($account->account_type), ['asset', 'expense']) && $balance['credit'] > 0) {
                    $issues[] = [
                        'type' => 'unusual_balance',
                        'message' => "Account {$balance['account_number']} ({$balance['account_name']}) has a credit balance",
                        'account' => $balance,
                    ];
                }

                // Liability, Equity, and Revenue accounts should normally have credit balances
                if (in_array(strtolower($account->account_type), ['liability', 'equity', 'revenue']) && $balance['debit'] > 0) {
                    $issues[] = [
                        'type' => 'unusual_balance',
                        'message' => "Account {$balance['account_number']} ({$balance['account_name']}) has a debit balance",
                        'account' => $balance,
                    ];
                }
            }
        }

        return $issues;
    }
}
