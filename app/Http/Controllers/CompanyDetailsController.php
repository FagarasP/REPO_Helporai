<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyDetailsController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->role !== 'company') {
            abort(403, 'Only company users can add company details.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:255'],
            'vat_id' => ['nullable', 'string', 'max:255'],
            'commercial_register_number' => ['nullable', 'string', 'max:255'],
            'currency' => ['required', 'string', 'max:3'],
            'financial_year_start' => ['nullable', 'date'],
            'financial_year_end' => ['nullable', 'date'],
            'logo' => ['nullable', 'image', 'max:2048'], // Max 2MB
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')->store('company-logos', 'public');
        }

        $company = Company::create($validated);
        $user->company_id = $company->id;
        $user->save();

        return redirect()->back()->with('success', 'Company details added successfully.');
    }

    public function update(Request $request, Company $company)
    {
        $user = auth()->user();

        if ($user->role !== 'company' || $user->company_id !== $company->id) {
            abort(403, 'Unauthorized to update these company details.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:255'],
            'vat_id' => ['nullable', 'string', 'max:255'],
            'commercial_register_number' => ['nullable', 'string', 'max:255'],
            'currency' => ['required', 'string', 'max:3'],
            'financial_year_start' => ['nullable', 'date'],
            'financial_year_end' => ['nullable', 'date'],
            'logo' => ['nullable', 'image', 'max:2048'], // Max 2MB
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('company-logos', 'public');
        }

        $company->update($validated);

        return redirect()->back()->with('success', 'Company details updated successfully.');
    }

    public function removeLogo(Company $company)
    {
        $user = auth()->user();

        if ($user->role !== 'company' || $user->company_id !== $company->id) {
            abort(403, 'Unauthorized to remove logo for this company.');
        }

        if ($company->logo_path) {
            Storage::disk('public')->delete($company->logo_path);
            $company->logo_path = null;
            $company->save();
        }

        return redirect()->back()->with('success', 'Company logo removed successfully.');
    }
}
