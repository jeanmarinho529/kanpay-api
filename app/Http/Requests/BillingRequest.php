<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BillingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            '*.batch_file_id' => 'required',
            '*.row_number' => 'required|integer',
            '*.name' => 'required|string',
            '*.government_id' => 'required|string',
            '*.email' => 'required|email',
            '*.debt_amount' => 'required|numeric',
            '*.debt_due_date' => 'required|date',
            '*.debt_id' => 'required|string',
        ];
    }
}
