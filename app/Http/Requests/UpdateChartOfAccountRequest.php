<?php

namespace App\Http\Requests;

use App\Models\ChartOfAccount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChartOfAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $accountId = $this->route('account')->id;

        return [
            'account_number' => [
                'required',
                'string',
                Rule::unique('chart_of_accounts')->ignore($accountId),
            ],
            'account_name' => 'required|string|max:255',
            'account_type' => ['required', Rule::in(array_keys(ChartOfAccount::ACCOUNT_TYPES))],
            'account_sub_type' => 'nullable|string',
            'description' => 'nullable|string',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'opening_balance' => 'nullable|numeric',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
        ];
    }
}
