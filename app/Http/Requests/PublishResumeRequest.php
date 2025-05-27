<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PublishResumeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids'   => ['required', 'array', 'size:1'],
            'ids.*' => ['required', 'integer', 'exists:resumes,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required'  => 'Please provide a resume ID.',
            'ids.array'     => 'The IDs must be provided as an array.',
            'ids.size'      => 'Exactly one resume must be selected.',
            'ids.*.exists'  => 'The selected resume does not exist.',
        ];
    }
}
