<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class JournalEntryController extends Controller
{
    public function index(): Response
    {
        $journalEntries = JournalEntry::with(['createdBy', 'lines.chartOfAccount'])
            ->where('company_id', auth()->user()->company_id)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('GeneralLedger/JournalEntries/Index', [
            'journalEntries' => $journalEntries,
        ]);
    }

    public function create(): Response
    {
        $chartOfAccounts = ChartOfAccount::active()
            ->where('company_id', auth()->user()->company_id)
            ->orderBy('account_number')
            ->get(['id', 'account_number', 'account_name', 'account_type']);

        return Inertia::render('GeneralLedger/JournalEntries/Create', [
            'chartOfAccounts' => $chartOfAccounts,
            'nextReference' => JournalEntry::generateReference(),
        ]);
    }

    public function store(Request $request)
    {
        \Log::info('Journal Entry Store Request:', $request->all());
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string|unique:journal_entries,reference',
            'date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'lines' => 'required|array|min:2',
            'lines.*.chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'lines.*.description' => 'nullable|string|max:500',
            'lines.*.debit' => 'required|numeric|min:0',
            'lines.*.credit' => 'required|numeric|min:0',
            'lines.*.vat_code' => 'nullable|string|max:10',
            'lines.*.vat_amount' => 'nullable|numeric|min:0',
            'lines.*.cost_center' => 'nullable|string|max:50',
            'lines.*.profit_center' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Validate that each line has either debit OR credit, not both or neither
        foreach ($request->lines as $line) {
            if (($line['debit'] > 0 && $line['credit'] > 0) ||
                ($line['debit'] == 0 && $line['credit'] == 0)) {
                return back()->withErrors([
                    'lines' => 'Each line must have either a debit or credit amount, not both or neither.',
                ])->withInput();
            }
        }

        // Calculate totals
        $totalDebit = collect($request->lines)->sum('debit');
        $totalCredit = collect($request->lines)->sum('credit');

        \Log::info('Totals calculated:', ['debit' => $totalDebit, 'credit' => $totalCredit]);

        // Validate balance
        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->withErrors([
                'balance' => 'Total debits must equal total credits.',
            ])->withInput();
        }

        try {
            DB::beginTransaction();

            $journalEntry = JournalEntry::create([
                'reference' => $request->reference,
                'date' => $request->date,
                'description' => $request->description,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'company_id' => auth()->user()->company_id,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->lines as $index => $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'chart_of_account_id' => $line['chart_of_account_id'],
                    'description' => $line['description'],
                    'debit' => $line['debit'],
                    'credit' => $line['credit'],
                    'vat_code' => $line['vat_code'],
                    'vat_amount' => $line['vat_amount'] ?? 0,
                    'cost_center' => $line['cost_center'],
                    'profit_center' => $line['profit_center'],
                    'line_order' => $index + 1,
                ]);
            }

            DB::commit();
            \Log::info('Journal entry saved successfully:', ['id' => $journalEntry->id]);

            return redirect()->route('journal-entries.show', $journalEntry)
                ->with('success', 'Journal entry created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Journal entry creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'general' => 'Error creating journal entry. Please try again.',
            ])->withInput();
        }
    }

    public function update(Request $request, JournalEntry $journalEntry)
    {
        if ($journalEntry->status !== 'draft') {
            return back()->withErrors([
                'general' => 'Cannot edit a posted or reversed journal entry.',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'reference' => 'required|string|unique:journal_entries,reference,'.$journalEntry->id,
            'date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'lines' => 'required|array|min:2',
            'lines.*.chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'lines.*.description' => 'nullable|string|max:500',
            'lines.*.debit' => 'required|numeric|min:0',
            'lines.*.credit' => 'required|numeric|min:0',
            'lines.*.vat_code' => 'nullable|string|max:10',
            'lines.*.vat_amount' => 'nullable|numeric|min:0',
            'lines.*.cost_center' => 'nullable|string|max:50',
            'lines.*.profit_center' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Validate lines and calculate totals
        foreach ($request->lines as $line) {
            if (($line['debit'] > 0 && $line['credit'] > 0) ||
                ($line['debit'] == 0 && $line['credit'] == 0)) {
                return back()->withErrors([
                    'lines' => 'Each line must have either a debit or credit amount, not both or neither.',
                ])->withInput();
            }
        }

        $totalDebit = collect($request->lines)->sum('debit');
        $totalCredit = collect($request->lines)->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->withErrors([
                'balance' => 'Total debits must equal total credits.',
            ])->withInput();
        }

        try {
            DB::beginTransaction();

            $journalEntry->update([
                'reference' => $request->reference,
                'date' => $request->date,
                'description' => $request->description,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
            ]);

            // Delete existing lines and create new ones
            $journalEntry->lines()->delete();

            foreach ($request->lines as $index => $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'chart_of_account_id' => $line['chart_of_account_id'],
                    'description' => $line['description'],
                    'debit' => $line['debit'],
                    'credit' => $line['credit'],
                    'vat_code' => $line['vat_code'],
                    'vat_amount' => $line['vat_amount'] ?? 0,
                    'cost_center' => $line['cost_center'],
                    'profit_center' => $line['profit_center'],
                    'line_order' => $index + 1,
                ]);
            }

            DB::commit();

            return redirect()->route('journal-entries.show', $journalEntry)
                ->with('success', 'Journal entry updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();

            return back()->withErrors([
                'general' => 'Error updating journal entry. Please try again.',
            ])->withInput();
        }
    }

    public function post(JournalEntry $journalEntry)
    {
        if (! $journalEntry->canBePosted()) {
            return back()->withErrors([
                'general' => 'Journal entry cannot be posted.',
            ]);
        }

        try {
            $journalEntry->update([
                'status' => 'posted',
                'posted_at' => now(),
            ]);

            return back()->with('success', 'Journal entry posted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors([
                'general' => 'Error posting journal entry.',
            ]);
        }
    }

    public function reverse(Request $request, JournalEntry $journalEntry)
    {
        if (! $journalEntry->canBeReversed()) {
            return back()->withErrors([
                'general' => 'Journal entry cannot be reversed.',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'reversal_reason' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            DB::beginTransaction();

            // Mark original as reversed
            $journalEntry->update([
                'status' => 'reversed',
                'reversed_at' => now(),
                'reversed_by' => auth()->id(),
                'reversal_reason' => $request->reversal_reason,
            ]);

            // Create reversing entry
            $reversingEntry = JournalEntry::create([
                'reference' => JournalEntry::generateReference(),
                'date' => now()->toDateString(),
                'description' => 'Reversal of '.$journalEntry->reference.' - '.$request->reversal_reason,
                'status' => 'posted',
                'total_debit' => $journalEntry->total_credit,
                'total_credit' => $journalEntry->total_debit,
                'company_id' => $journalEntry->company_id,
                'created_by' => auth()->id(),
                'posted_at' => now(),
            ]);

            // Create reversed lines
            foreach ($journalEntry->lines as $index => $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $reversingEntry->id,
                    'chart_of_account_id' => $line->chart_of_account_id,
                    'description' => 'Reversal: '.$line->description,
                    'debit' => $line->credit,
                    'credit' => $line->debit,
                    'vat_code' => $line->vat_code,
                    'vat_amount' => -$line->vat_amount,
                    'cost_center' => $line->cost_center,
                    'profit_center' => $line->profit_center,
                    'line_order' => $index + 1,
                ]);
            }

            DB::commit();

            return redirect()->route('journal-entries.show', $reversingEntry)
                ->with('success', 'Journal entry reversed successfully.');

        } catch (\Exception $e) {
            DB::rollback();

            return back()->withErrors([
                'general' => 'Error reversing journal entry.',
            ]);
        }
    }

    public function show(JournalEntry $journalEntry): Response
    {
        $journalEntry->load(['lines.chartOfAccount', 'createdBy', 'reversedBy']);

        return Inertia::render('GeneralLedger/JournalEntries/Show', [
            'journalEntry' => $journalEntry,
        ]);
    }

    public function edit(JournalEntry $journalEntry): Response
    {
        if ($journalEntry->status !== 'draft') {
            abort(403, 'Cannot edit a posted or reversed journal entry.');
        }

        $chartOfAccounts = ChartOfAccount::active()
            ->where('company_id', auth()->user()->company_id)
            ->orderBy('account_number')
            ->get(['id', 'account_number', 'account_name', 'account_type']);

        $journalEntry->load('lines.chartOfAccount');

        return Inertia::render('GeneralLedger/JournalEntries/Edit', [
            'journalEntry' => $journalEntry,
            'chartOfAccounts' => $chartOfAccounts,
        ]);
    }

    // public function update(Request $request, JournalEntry $journalEntry): JsonResponse
    // {
    //     if ($journalEntry->status !== 'draft') {
    //         return response()->json(['message' => 'Cannot edit a posted or reversed journal entry.'], 403);
    //     }

    //     $validator = Validator::make($request->all(), [
    //         'reference' => 'required|string|unique:journal_entries,reference,' . $journalEntry->id,
    //         'date' => 'required|date',
    //         'description' => 'nullable|string|max:1000',
    //         'lines' => 'required|array|min:2',
    //         'lines.*.chart_of_account_id' => 'required|exists:chart_of_accounts,id',
    //         'lines.*.description' => 'nullable|string|max:500',
    //         'lines.*.debit' => 'required|numeric|min:0',
    //         'lines.*.credit' => 'required|numeric|min:0',
    //         'lines.*.vat_code' => 'nullable|string|max:10',
    //         'lines.*.vat_amount' => 'nullable|numeric|min:0',
    //         'lines.*.cost_center' => 'nullable|string|max:50',
    //         'lines.*.profit_center' => 'nullable|string|max:50'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     // Validate lines and calculate totals (same as store method)
    //     foreach ($request->lines as $line) {
    //         if (($line['debit'] > 0 && $line['credit'] > 0) ||
    //             ($line['debit'] == 0 && $line['credit'] == 0)) {
    //             return response()->json([
    //                 'errors' => ['lines' => ['Each line must have either a debit or credit amount, not both or neither.']]
    //             ], 422);
    //         }
    //     }

    //     $totalDebit = collect($request->lines)->sum('debit');
    //     $totalCredit = collect($request->lines)->sum('credit');

    //     if (abs($totalDebit - $totalCredit) > 0.01) {
    //         return response()->json([
    //             'errors' => ['balance' => ['Total debits must equal total credits.']]
    //         ], 422);
    //     }

    //     try {
    //         DB::beginTransaction();

    //         $journalEntry->update([
    //             'reference' => $request->reference,
    //             'date' => $request->date,
    //             'description' => $request->description,
    //             'total_debit' => $totalDebit,
    //             'total_credit' => $totalCredit
    //         ]);

    //         // Delete existing lines and create new ones
    //         $journalEntry->lines()->delete();

    //         foreach ($request->lines as $index => $line) {
    //             JournalEntryLine::create([
    //                 'journal_entry_id' => $journalEntry->id,
    //                 'chart_of_account_id' => $line['chart_of_account_id'],
    //                 'description' => $line['description'],
    //                 'debit' => $line['debit'],
    //                 'credit' => $line['credit'],
    //                 'vat_code' => $line['vat_code'],
    //                 'vat_amount' => $line['vat_amount'] ?? 0,
    //                 'cost_center' => $line['cost_center'],
    //                 'profit_center' => $line['profit_center'],
    //                 'line_order' => $index + 1
    //             ]);
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'message' => 'Journal entry updated successfully.',
    //             'journal_entry' => $journalEntry->fresh()->load('lines.chartOfAccount')
    //         ]);

    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return response()->json(['message' => 'Error updating journal entry.'], 500);
    //     }
    // }

    public function destroy(JournalEntry $journalEntry): JsonResponse
    {
        if ($journalEntry->status !== 'draft') {
            return response()->json(['message' => 'Cannot delete a posted or reversed journal entry.'], 403);
        }

        try {
            $journalEntry->delete();

            return response()->json(['message' => 'Journal entry deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting journal entry.'], 500);
        }
    }

    // public function post(JournalEntry $journalEntry): JsonResponse
    // {
    //     if (!$journalEntry->canBePosted()) {
    //         return response()->json(['message' => 'Journal entry cannot be posted.'], 400);
    //     }

    //     try {
    //         $journalEntry->update([
    //             'status' => 'posted',
    //             'posted_at' => now()
    //         ]);

    //         return response()->json(['message' => 'Journal entry posted successfully.']);
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => 'Error posting journal entry.'], 500);
    //     }
    // }

    // public function reverse(Request $request, JournalEntry $journalEntry): JsonResponse
    // {
    //     if (!$journalEntry->canBeReversed()) {
    //         return response()->json(['message' => 'Journal entry cannot be reversed.'], 400);
    //     }

    //     $validator = Validator::make($request->all(), [
    //         'reversal_reason' => 'required|string|max:1000'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     try {
    //         DB::beginTransaction();

    //         // Mark original as reversed
    //         $journalEntry->update([
    //             'status' => 'reversed',
    //             'reversed_at' => now(),
    //             'reversed_by' => auth()->id(),
    //             'reversal_reason' => $request->reversal_reason
    //         ]);

    //         // Create reversing entry
    //         $reversingEntry = JournalEntry::create([
    //             'reference' => JournalEntry::generateReference(),
    //             'date' => now()->toDateString(),
    //             'description' => 'Reversal of ' . $journalEntry->reference . ' - ' . $request->reversal_reason,
    //             'status' => 'posted',
    //             'total_debit' => $journalEntry->total_credit, // Swap totals
    //             'total_credit' => $journalEntry->total_debit,
    //             'company_id' => $journalEntry->company_id,
    //             'created_by' => auth()->id(),
    //             'posted_at' => now()
    //         ]);

    //         // Create reversed lines (swap debits and credits)
    //         foreach ($journalEntry->lines as $index => $line) {
    //             JournalEntryLine::create([
    //                 'journal_entry_id' => $reversingEntry->id,
    //                 'chart_of_account_id' => $line->chart_of_account_id,
    //                 'description' => 'Reversal: ' . $line->description,
    //                 'debit' => $line->credit, // Swap debit/credit
    //                 'credit' => $line->debit,
    //                 'vat_code' => $line->vat_code,
    //                 'vat_amount' => -$line->vat_amount, // Reverse VAT
    //                 'cost_center' => $line->cost_center,
    //                 'profit_center' => $line->profit_center,
    //                 'line_order' => $index + 1
    //             ]);
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'message' => 'Journal entry reversed successfully.',
    //             'reversing_entry' => $reversingEntry->load('lines.chartOfAccount')
    //         ]);

    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return response()->json(['message' => 'Error reversing journal entry.'], 500);
    //     }
    // }
}
