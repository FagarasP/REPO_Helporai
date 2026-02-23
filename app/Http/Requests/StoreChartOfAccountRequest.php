<?php

namespace App\Http\Requests;

use App\Models\ChartOfAccount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreChartOfAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Handle authorization in controller or policy
    }

    public function rules(): array
    {
        return [
            'account_number' => 'required|string|unique:chart_of_accounts,account_number',
            'account_name' => 'required|string|max:255',
            'account_type' => ['required', Rule::in(array_keys(ChartOfAccount::ACCOUNT_TYPES))],
            'account_sub_type' => 'nullable|string',
            'description' => 'nullable|string',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'opening_balance' => 'nullable|numeric',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
        ];
    }

    public function messages(): array
    {
        return [
            'account_number.required' => 'Account number is required.',
            'account_number.unique' => 'This account number already exists.',
            'account_name.required' => 'Account name is required.',
            'account_type.required' => 'Account type is required.',
            'account_type.in' => 'Invalid account type selected.',
        ];
    }
}
