<?php

// app/Services/ClosingEntryService.php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\ClosingEntry;
use App\Models\Company;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ClosingEntryService
{
    public function previewClosingEntries(Company $company, Carbon $periodStart, Carbon $periodEnd): array
    {
        // Get retained earnings account
        $retainedEarningsAccount = $this->getRetainedEarningsAccount($company);

        if (! $retainedEarningsAccount) {
            throw new \Exception('No Retained Earnings account found. Please create an Equity account with sub-type "retained_earnings".');
        }

        // Get revenue accounts with balances
        $revenueAccounts = $this->getAccountBalances($company, 'revenue', $periodStart, $periodEnd);

        // Get expense accounts with balances
        $expenseAccounts = $this->getAccountBalances($company, 'expense', $periodStart, $periodEnd);

        $totalRevenue = collect($revenueAccounts)->sum('balance');
        $totalExpense = collect($expenseAccounts)->sum('balance');
        $netIncome = $totalRevenue - abs($totalExpense);

        return [
            'revenue_accounts' => $revenueAccounts,
            'expense_accounts' => $expenseAccounts,
            'retained_earnings_account' => $retainedEarningsAccount,
            'total_revenue' => $totalRevenue,
            'total_expense' => abs($totalExpense),
            'net_income' => $netIncome,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
        ];
    }

    public function generateClosingEntry(
        Company $company,
        Carbon $periodStart,
        Carbon $periodEnd,
        Carbon $closingDate,
        string $periodDescription,
        int $userId
    ): ClosingEntry {
        return DB::transaction(function () use ($company, $periodStart, $periodEnd, $closingDate, $periodDescription, $userId) {
            // Check if closing already exists for this period
            $existingClosing = ClosingEntry::where('company_id', $company->id)
                ->where('period_start', $periodStart)
                ->where('period_end', $periodEnd)
                ->where('status', '!=', 'reversed')
                ->first();

            if ($existingClosing) {
                throw new \Exception('Closing entry already exists for this period.');
            }

            $preview = $this->previewClosingEntries($company, $periodStart, $periodEnd);

            // Create the closing entry record
            $closingEntry = ClosingEntry::create([
                'company_id' => $company->id,
                'closing_date' => $closingDate,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'period_description' => $periodDescription,
                'total_revenue_closed' => $preview['total_revenue'],
                'total_expense_closed' => $preview['total_expense'],
                'net_income' => $preview['net_income'],
                'retained_earnings_account_id' => $preview['retained_earnings_account']->id,
                'status' => 'draft',
                'closed_accounts' => [
                    'revenue' => $preview['revenue_accounts'],
                    'expense' => $preview['expense_accounts'],
                ],
                'created_by' => $userId,
            ]);

            // Generate journal entry reference
            $jeReference = 'CE-'.$periodEnd->format('Y').'-'.$closingEntry->id;

            // Create the closing journal entry
            $journalEntry = JournalEntry::create([
                'reference' => $jeReference,
                'date' => $closingDate,
                'description' => "Closing Entry: {$periodDescription}",
                'status' => 'posted',
                'company_id' => $company->id,
                'created_by' => $userId,
                'posted_at' => now(),
            ]);

            $totalDebit = 0;
            $totalCredit = 0;
            $lineOrder = 1;

            // Close revenue accounts (Debit Revenue, Credit Retained Earnings)
            foreach ($preview['revenue_accounts'] as $account) {
                if ($account['balance'] > 0) {
                    JournalEntryLine::create([
                        'journal_entry_id' => $journalEntry->id,
                        'chart_of_account_id' => $account['account_id'],
                        'description' => "Closing {$account['account_name']}",
                        'debit' => $account['balance'],
                        'credit' => 0,
                        'line_order' => $lineOrder++,
                    ]);
                    $totalDebit += $account['balance'];
                }
            }

            // Close expense accounts (Credit Expense, Debit Retained Earnings)
            foreach ($preview['expense_accounts'] as $account) {
                if ($account['balance'] > 0) {
                    JournalEntryLine::create([
                        'journal_entry_id' => $journalEntry->id,
                        'chart_of_account_id' => $account['account_id'],
                        'description' => "Closing {$account['account_name']}",
                        'debit' => 0,
                        'credit' => abs($account['balance']),
                        'line_order' => $lineOrder++,
                    ]);
                    $totalCredit += abs($account['balance']);
                }
            }

            // Net effect to Retained Earnings
            if ($preview['net_income'] != 0) {
                if ($preview['net_income'] > 0) {
                    // Profit: Credit Retained Earnings
                    JournalEntryLine::create([
                        'journal_entry_id' => $journalEntry->id,
                        'chart_of_account_id' => $preview['retained_earnings_account']->id,
                        'description' => "Net Income for {$periodDescription}",
                        'debit' => 0,
                        'credit' => $preview['net_income'],
                        'line_order' => $lineOrder++,
                    ]);
                    $totalCredit += $preview['net_income'];
                } else {
                    // Loss: Debit Retained Earnings
                    JournalEntryLine::create([
                        'journal_entry_id' => $journalEntry->id,
                        'chart_of_account_id' => $preview['retained_earnings_account']->id,
                        'description' => "Net Loss for {$periodDescription}",
                        'debit' => abs($preview['net_income']),
                        'credit' => 0,
                        'line_order' => $lineOrder++,
                    ]);
                    $totalDebit += abs($preview['net_income']);
                }
            }

            // Update journal entry totals
            $journalEntry->update([
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
            ]);

            // Update closing entry with journal reference and mark as posted
            $closingEntry->update([
                'journal_entry_reference' => $jeReference,
                'status' => 'posted',
                'posted_at' => now(),
            ]);

            return $closingEntry;
        });
    }

    private function getRetainedEarningsAccount(Company $company): ?ChartOfAccount
    {
        return ChartOfAccount::where('company_id', $company->id)
            ->where('account_type', 'equity')
            ->where('account_sub_type', 'retained_earnings')
            ->where('is_active', true)
            ->first();
    }

    private function getAccountBalances(Company $company, string $accountType, Carbon $periodStart, Carbon $periodEnd): array
    {
        $accounts = ChartOfAccount::where('company_id', $company->id)
            ->where('account_type', $accountType)
            ->where('is_active', true)
            ->get();

        $balances = [];

        foreach ($accounts as $account) {
            $balance = $this->calculateAccountBalance($account, $periodStart, $periodEnd);

            if (abs($balance) >= 0.01) {
                $balances[] = [
                    'account_id' => $account->id,
                    'account_number' => $account->account_number,
                    'account_name' => $account->account_name,
                    'account_type' => $account->account_type,
                    'balance' => abs($balance),
                ];
            }
        }

        return $balances;
    }

    private function calculateAccountBalance(ChartOfAccount $account, Carbon $periodStart, Carbon $periodEnd): float
    {
        $result = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entry_lines.chart_of_account_id', $account->id)
            ->where('journal_entries.company_id', $account->company_id)
            ->where('journal_entries.date', '>=', $periodStart)
            ->where('journal_entries.date', '<=', $periodEnd)
            ->where('journal_entries.status', 'posted')
            ->selectRaw('SUM(journal_entry_lines.debit - journal_entry_lines.credit) as balance')
            ->first();

        return (float) ($result->balance ?? 0);
    }
}
