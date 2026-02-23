<?php

// app/Console/Commands/GenerateTrialBalance.php (continued)

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Company;
use App\Models\TrialBalanceSnapshot;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateTrialBalance extends Command
{
    protected $signature = 'trial-balance:generate {company_id} {--date=} {--user_id=1}';

    protected $description = 'Generate trial balance for a specific company';

    public function handle()
    {
        $companyId = $this->argument('company_id');
        $date = $this->option('date') ?: Carbon::today()->toDateString();
        $userId = $this->option('user_id');

        $company = Company::findOrFail($companyId);
        $asOfDate = Carbon::parse($date);

        $this->info("Generating trial balance for {$company->name} as of {$date}");

        // Get all active accounts for the company
        $accounts = Account::where('company_id', $company->id)
            ->where('is_active', true)
            ->orderBy('account_number')
            ->get();

        $balances = [];
        $totalDebits = 0;
        $totalCredits = 0;

        $this->info('Processing '.$accounts->count().' accounts...');

        $progressBar = $this->output->createProgressBar($accounts->count());
        $progressBar->start();

        foreach ($accounts as $account) {
            $balance = $account->getBalanceAsOfDate($asOfDate);

            if ($balance != 0) {
                $accountData = [
                    'account_id' => $account->id,
                    'account_number' => $account->account_number,
                    'account_name' => $account->account_name,
                    'account_type' => $account->account_type,
                    'balance' => $balance,
                    'debit' => 0,
                    'credit' => 0,
                ];

                // Determine if balance should be shown as debit or credit
                if ($account->isDebitAccount()) {
                    if ($balance > 0) {
                        $accountData['debit'] = $balance;
                        $totalDebits += $balance;
                    } else {
                        $accountData['credit'] = abs($balance);
                        $totalCredits += abs($balance);
                    }
                } else {
                    if ($balance > 0) {
                        $accountData['credit'] = $balance;
                        $totalCredits += $balance;
                    } else {
                        $accountData['debit'] = abs($balance);
                        $totalDebits += abs($balance);
                    }
                }

                $balances[] = $accountData;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        // Create snapshot
        $snapshot = TrialBalanceSnapshot::create([
            'company_id' => $company->id,
            'as_of_date' => $asOfDate,
            'balances' => $balances,
            'total_debits' => $totalDebits,
            'total_credits' => $totalCredits,
            'is_balanced' => abs($totalDebits - $totalCredits) < 0.01,
            'generated_by' => $userId,
        ]);

        $this->info('Trial balance generated successfully!');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Debits', number_format($totalDebits, 2)],
                ['Total Credits', number_format($totalCredits, 2)],
                ['Difference', number_format($totalDebits - $totalCredits, 2)],
                ['Balanced', $snapshot->is_balanced ? 'Yes' : 'No'],
                ['Accounts with Balance', count($balances)],
            ]
        );

        return Command::SUCCESS;
    }
}
