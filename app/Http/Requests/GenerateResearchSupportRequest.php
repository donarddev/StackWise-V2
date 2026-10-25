<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateResearchSupportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'study_title' => ['nullable', 'string', 'max:255'],
            'study_context' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
