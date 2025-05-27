<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkDeleteResumesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids'   => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:resumes,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required'  => 'You must provide a list of resume IDs.',
            'ids.array'     => 'The resume IDs must be in an array format.',
            'ids.*.exists'  => 'One or more resume IDs do not exist.',
        ];
    }
}
