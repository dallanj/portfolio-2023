<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkMarkReadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids'   => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:contacts,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required'  => 'Please provide at least one contact ID.',
            'ids.array'     => 'The IDs must be provided as an array.',
            'ids.*.exists'  => 'One or more of the selected contacts do not exist.',
        ];
    }
}
