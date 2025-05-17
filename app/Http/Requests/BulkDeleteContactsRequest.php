<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkDeleteContactsRequest extends FormRequest
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
            'ids.required'  => 'You must provide a list of contact IDs.',
            'ids.array'     => 'The contact IDs must be in an array format.',
            'ids.*.exists'  => 'One or more contact IDs do not exist.',
        ];
    }
}
