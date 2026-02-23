<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateTrialBalanceRequest;
use App\Http\Resources\TrialBalanceResource;
use App\Models\TrialBalanceSnapshot;
use App\Services\TrialBalanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TrialBalanceController extends Controller
{
    public function __construct(
        private TrialBalanceService $trialBalanceService
    ) {}

    public function index(): Response
    {
        $user = auth()->user();
        $company = $user->company;

        if (! $company) {
            return Inertia::render('GeneralLedger/TrialBalance', [
                'recentSnapshots' => [],
                'hasCompany' => false,
                'error' => 'No company associated with your account. Please contact your administrator.',
            ]);
        }

        $recentSnapshots = TrialBalanceSnapshot::where('company_id', $company->id)
            ->orderBy('as_of_date', 'desc')
            ->limit(5)
            ->with('generatedBy')
            ->get();

        return Inertia::render('GeneralLedger/TrialBalance', [
            // Convert the resource collection to array to avoid the "data" wrapper
            'recentSnapshots' => TrialBalanceResource::collection($recentSnapshots)->resolve(),
            'hasCompany' => true,
            'error' => null,
        ]);
    }

    public function generate(GenerateTrialBalanceRequest $request)
    {
        try {
            $company = auth()->user()->company;

            if (! $company) {
                return back()->withErrors(['error' => 'No company assigned to your account']);
            }

            $asOfDate = Carbon::parse($request->validated('as_of_date'));

            $snapshot = $this->trialBalanceService->generateTrialBalance(
                $company,
                $asOfDate,
                auth()->id()
            );

            $issues = $this->trialBalanceService->validateTrialBalance($snapshot);

            return back()->with('success', 'Trial balance generated successfully');
        } catch (\Exception $e) {
            \Log::error('Trial Balance Generation Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['error' => 'Failed to generate trial balance: '.$e->getMessage()]);
        }
    }

    public function show(TrialBalanceSnapshot $snapshot)
    {
        $snapshot->load('generatedBy');

        return response()->json([
            'snapshot' => new TrialBalanceResource($snapshot),
        ]);
    }

    public function export(TrialBalanceSnapshot $snapshot, Request $request)
    {
        $format = $request->get('format', 'pdf');

        switch ($format) {
            case 'excel':
                return $this->exportToExcel($snapshot);
            case 'csv':
                return $this->exportToCsv($snapshot);
            default:
                return $this->exportToPdf($snapshot);
        }
    }

    private function exportToPdf(TrialBalanceSnapshot $snapshot)
    {
        // Implementation for PDF export
    }

    private function exportToExcel(TrialBalanceSnapshot $snapshot)
    {
        // Implementation for Excel export
    }

    private function exportToCsv(TrialBalanceSnapshot $snapshot)
    {
        // Implementation for CSV export
    }
}
