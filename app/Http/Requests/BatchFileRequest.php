<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BatchFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:csv',
            'file_type_name' => 'required|string|max:50|exists:batch_file_types,name',
        ];
    }
}
