<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecommendationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_name' => ['required', 'string', 'max:255'],
            'project_type' => ['required', 'string'],
            'team_size' => ['required', 'integer', 'min:1'],
            'complexity' => ['required', 'string'],
            'preferred_platform' => ['required', 'string'],
            'development_experience' => ['required', 'string'],
            'timeline' => ['required', 'string'],
            'project_goal' => ['required', 'string', 'max:1000'],
        ];
    }
}