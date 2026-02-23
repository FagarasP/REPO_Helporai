<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateTrialBalanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        // return auth()->check();
        return true;
        // return $this->user()->can('create', TrialBalanceSnapshot::class);
    }

    public function rules(): array
    {
        return [
            'as_of_date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'as_of_date.required' => 'The as of date is required.',
            'as_of_date.date' => 'The as of date must be a valid date.',
            'as_of_date.before_or_equal' => 'The as of date cannot be in the future.',
        ];
    }
}
