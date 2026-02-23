<?php

// app/Http/Requests/GenerateClosingEntryRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateClosingEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'closing_date' => 'required|date|after_or_equal:period_end',
            'period_description' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'period_end.after_or_equal' => 'Period end date must be after or equal to period start date.',
            'closing_date.after_or_equal' => 'Closing date must be after or equal to period end date.',
        ];
    }
}
