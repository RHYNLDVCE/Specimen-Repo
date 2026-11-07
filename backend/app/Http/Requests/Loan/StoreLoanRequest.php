<?php

namespace App\Http\Requests\Loan;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'specimen_id' => ['required', 'string'],
            'researcher_id' => ['required', 'string'],
            'borrower_name' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string', 'max:500'],
            'loan_date' => ['required', 'date'],
            'expected_return_date' => ['required', 'date', 'after_or_equal:loan_date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
