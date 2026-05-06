<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentationFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string|\Illuminate\Validation\Rules\In>>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', Rule::in(['all', 'languages', 'frameworks', 'sdlc_models'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $category = $this->input('category', 'all');
        if (! in_array($category, ['all', 'languages', 'frameworks', 'sdlc_models'], true)) {
            $category = 'all';
        }

        $this->merge([
            'search' => $this->input('search', ''),
            'category' => $category,
        ]);
    }
}
