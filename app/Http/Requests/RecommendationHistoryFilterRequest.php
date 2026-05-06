<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class RecommendationHistoryFilterRequest extends FormRequest
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
            'project_type' => ['nullable', 'string', 'max:255'],
            'language' => ['nullable', 'string', 'max:255'],
            'framework' => ['nullable', 'string', 'max:255'],
            'sdlc_model' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', 'string', Rule::in(['latest', 'oldest', 'confidence_desc', 'confidence_asc'])],
            'confidence_min' => ['nullable', 'integer', 'min:0', 'max:100'],
            'confidence_max' => ['nullable', 'integer', 'min:0', 'max:100'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $min = $this->input('confidence_min');
            $max = $this->input('confidence_max');
            if ($min !== null && $min !== '' && $max !== null && $max !== '' && (int) $min > (int) $max) {
                $validator->errors()->add('confidence_max', 'Maximum confidence must be greater than or equal to minimum.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'search' => $this->input('search', ''),
            'sort' => $this->input('sort', 'latest'),
        ]);
    }
}
