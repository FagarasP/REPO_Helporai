<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ChartOfAccountController extends Controller
{
    public function index(Request $request): Response
    {
        $query = ChartOfAccount::with(['parent', 'children'])
            ->byCompany(auth()->user()->company_id);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('account_name', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('account_type')) {
            $query->byType($request->account_type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $accounts = $query->orderBy('account_number')->paginate(15);

        return Inertia::render('GeneralLedger/ChartOfAccounts', [
            'accounts' => $accounts,
            'filters' => $request->only(['search', 'account_type', 'status']),
            'accountTypes' => ChartOfAccount::ACCOUNT_TYPES,
            'accountSubTypes' => ChartOfAccount::ACCOUNT_SUB_TYPES,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_number' => 'required|string|unique:chart_of_accounts,account_number',
            'account_name' => 'required|string|max:255',
            'account_type' => ['required', Rule::in(array_keys(ChartOfAccount::ACCOUNT_TYPES))],
            'account_sub_type' => 'nullable|string',
            'description' => 'nullable|string',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'opening_balance' => 'nullable|numeric',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
        ]);

        $validated['company_id'] = auth()->user()->company_id;
        $validated['current_balance'] = $validated['opening_balance'] ?? 0;

        ChartOfAccount::create($validated);

        return redirect()->route('chart-of-accounts.index')->with('success', 'Account created successfully');
    }

    public function show(ChartOfAccount $account)
    {
        // Simple company check instead of authorize
        if ($account->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $account->load(['parent', 'children', 'journalEntries' => function ($query) {
            $query->latest()->take(10);
        }]);

        return Inertia::render('GeneralLedger/ViewAccount', [
            'account' => $account,
        ]);
    }

    public function update(Request $request, ChartOfAccount $account)
    {
        // Simple company check instead of authorize
        if ($account->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'account_number' => [
                'required',
                'string',
                Rule::unique('chart_of_accounts')->ignore($account->id),
            ],
            'account_name' => 'required|string|max:255',
            'account_type' => ['required', Rule::in(array_keys(ChartOfAccount::ACCOUNT_TYPES))],
            'account_sub_type' => 'nullable|string',
            'description' => 'nullable|string',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'opening_balance' => 'nullable|numeric',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
        ]);

        // Prevent circular references
        if (isset($validated['parent_id']) && $validated['parent_id'] == $account->id) {
            return back()->withErrors(['parent_id' => 'An account cannot be its own parent']);
        }

        $account->update($validated);

        return redirect()->route('chart-of-accounts.index')->with('success', 'Account updated successfully');
    }

    public function destroy(ChartOfAccount $account)
    {
        // Simple company check instead of authorize
        if ($account->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized');
        }

        if (! $account->canBeDeleted()) {
            return back()->withErrors(['delete' => 'Cannot delete account with existing transactions or child accounts. Consider making it inactive instead.']);
        }

        $account->delete();

        return redirect()->route('chart-of-accounts.index')->with('success', 'Account deleted successfully');
    }

    public function toggleStatus(ChartOfAccount $account)
    {
        // Simple company check instead of authorize
        if ($account->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $account->update(['is_active' => ! $account->is_active]);

        $message = $account->is_active ? 'Account activated successfully' : 'Account deactivated successfully';

        return redirect()->route('chart-of-accounts.index')->with('success', $message);
    }
}
