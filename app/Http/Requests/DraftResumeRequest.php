<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DraftResumeRequest extends FormRequest
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
            'ids.required'  => 'Please provide at least one resume ID.',
            'ids.array'     => 'The IDs must be provided as an array.',
            'ids.*.exists'  => 'One or more of the selected resumes do not exist.',
        ];
    }
}
