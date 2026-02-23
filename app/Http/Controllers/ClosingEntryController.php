<?php

// app/Http/Controllers/ClosingEntryController.php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateClosingEntryRequest;
use App\Http\Resources\ClosingEntryResource;
use App\Models\ClosingEntry;
use App\Services\ClosingEntryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClosingEntryController extends Controller
{
    public function __construct(
        private ClosingEntryService $closingEntryService
    ) {}

    public function index(): Response
    {
        $user = auth()->user();
        $company = $user->company;

        if (! $company) {
            return Inertia::render('GeneralLedger/ClosingEntries', [
                'recentClosingEntries' => [],
                'hasCompany' => false,
                'error' => 'No company associated with your account. Please contact your administrator.',
            ]);
        }

        $recentClosingEntries = ClosingEntry::where('company_id', $company->id)
            ->orderBy('closing_date', 'desc')
            ->limit(10)
            ->with(['createdBy', 'retainedEarningsAccount'])
            ->get();

        return Inertia::render('GeneralLedger/ClosingEntries', [
            'recentClosingEntries' => ClosingEntryResource::collection($recentClosingEntries)->resolve(),
            'hasCompany' => true,
            'error' => null,
        ]);
    }

    public function preview(Request $request)
    {
        \Log::info('=== PREVIEW REQUEST START ===');
        \Log::info('Request data:', $request->all());

        $validated = $request->validate([
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
        ]);

        try {
            $company = auth()->user()->company;
            \Log::info('Company:', ['company_id' => $company?->id]);

            if (! $company) {
                \Log::error('No company found');

                return back()->withErrors(['error' => 'No company assigned to your account']);
            }

            $periodStart = Carbon::parse($validated['period_start']);
            $periodEnd = Carbon::parse($validated['period_end']);

            \Log::info('Parsed dates:', [
                'period_start' => $periodStart->toDateString(),
                'period_end' => $periodEnd->toDateString(),
            ]);

            $preview = $this->closingEntryService->previewClosingEntries(
                $company,
                $periodStart,
                $periodEnd
            );

            \Log::info('Preview generated successfully:', [
                'revenue_count' => count($preview['revenue_accounts']),
                'expense_count' => count($preview['expense_accounts']),
                'total_revenue' => $preview['total_revenue'],
                'total_expense' => $preview['total_expense'],
                'net_income' => $preview['net_income'],
            ]);

            // Get recent closing entries for the index page
            $recentClosingEntries = ClosingEntry::where('company_id', $company->id)
                ->orderBy('closing_date', 'desc')
                ->limit(10)
                ->with(['createdBy', 'retainedEarningsAccount'])
                ->get();

            // Return to the same page with preview data
            // return Inertia::render('GeneralLedger/ClosingEntries', [
            //     'recentClosingEntries' => ClosingEntryResource::collection($recentClosingEntries)->resolve(),
            //     'hasCompany' => true,
            //     'error' => null,
            //     'preview' => $preview, // Include the preview data
            //     'success' => 'Preview generated successfully'
            // ]);
            return response()->json([
                'recentClosingEntries' => ClosingEntryResource::collection($recentClosingEntries)->resolve(),
                'hasCompany' => true,
                'error' => null,
                'preview' => $preview, // Include the preview data
                'success' => 'Preview generated successfully',
            ]);

        } catch (\Exception $e) {
            \Log::error('Closing Entry Preview Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Get recent closing entries for error case too
            $recentClosingEntries = ClosingEntry::where('company_id', auth()->user()->company_id)
                ->orderBy('closing_date', 'desc')
                ->limit(10)
                ->with(['createdBy', 'retainedEarningsAccount'])
                ->get();

            return Inertia::render('GeneralLedger/ClosingEntries', [
                'recentClosingEntries' => ClosingEntryResource::collection($recentClosingEntries)->resolve(),
                'hasCompany' => true,
                'error' => $e->getMessage(),
                'preview' => null,
            ]);
        }
    }

    public function generate(GenerateClosingEntryRequest $request)
    {
        \Log::info('=== GENERATE REQUEST START ===');
        \Log::info('Request data:', $request->all());

        try {
            $company = auth()->user()->company;

            if (! $company) {
                \Log::error('No company found for generate');
                // Use Inertia::render instead of back()
                $recentClosingEntries = ClosingEntry::where('company_id', 1) // fallback company
                    ->orderBy('closing_date', 'desc')->limit(10)
                    ->with(['createdBy', 'retainedEarningsAccount'])->get();

                return Inertia::render('GeneralLedger/ClosingEntries', [
                    'recentClosingEntries' => ClosingEntryResource::collection($recentClosingEntries)->resolve(),
                    'hasCompany' => false,
                    'error' => 'No company assigned to your account',
                ]);
            }

            $validated = $request->validated();

            $periodStart = Carbon::parse($validated['period_start']);
            $periodEnd = Carbon::parse($validated['period_end']);
            $closingDate = Carbon::parse($validated['closing_date']);
            $periodDescription = $validated['period_description'];

            $closingEntry = $this->closingEntryService->generateClosingEntry(
                $company,
                $periodStart,
                $periodEnd,
                $closingDate,
                $periodDescription,
                auth()->id()
            );

            \Log::info('Closing entry generated successfully', ['id' => $closingEntry->id]);

            // Get updated recent closing entries
            $recentClosingEntries = ClosingEntry::where('company_id', $company->id)
                ->orderBy('closing_date', 'desc')
                ->limit(10)
                ->with(['createdBy', 'retainedEarningsAccount'])
                ->get();

            // Use Inertia::render instead of back()
            return Inertia::render('GeneralLedger/ClosingEntries', [
                'recentClosingEntries' => ClosingEntryResource::collection($recentClosingEntries)->resolve(),
                'hasCompany' => true,
                'error' => null,
                'success' => 'Closing entry generated successfully!',
            ]);

        } catch (\Exception $e) {
            \Log::error('Closing Entry Generation Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Get recent closing entries for error case
            $recentClosingEntries = ClosingEntry::where('company_id', auth()->user()->company_id)
                ->orderBy('closing_date', 'desc')
                ->limit(10)
                ->with(['createdBy', 'retainedEarningsAccount'])
                ->get();

            // Use Inertia::render instead of back()
            return Inertia::render('GeneralLedger/ClosingEntries', [
                'recentClosingEntries' => ClosingEntryResource::collection($recentClosingEntries)->resolve(),
                'hasCompany' => true,
                'error' => $e->getMessage(),
                'preview' => null,
            ]);
        }
    }

    public function show(ClosingEntry $closingEntry)
    {
        \Log::info('Show closing entry request', ['id' => $closingEntry->id]);

        // Ensure user can only view their company's closing entries
        if ($closingEntry->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        try {
            $closingEntry->load(['createdBy', 'retainedEarningsAccount', 'journalEntry.journalEntryLines.chartOfAccount']);

            $journalEntryLines = $closingEntry->journalEntry ?
                $closingEntry->journalEntry->journalEntryLines->map(function ($line) {
                    return [
                        'account_number' => $line->chartOfAccount->account_number,
                        'account_name' => $line->chartOfAccount->account_name,
                        'description' => $line->description,
                        'debit' => $line->debit,
                        'credit' => $line->credit,
                    ];
                }) : [];

            // Return JSON response for modal (not Inertia redirect)
            return response()->json([
                'closingEntry' => new ClosingEntryResource($closingEntry),
                'journalEntryLines' => $journalEntryLines,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error showing closing entry', [
                'id' => $closingEntry->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Failed to load closing entry details'], 500);
        }
    }
}
