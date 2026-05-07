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
            'selected_features' => ['required', 'array', 'min:1'],
            'selected_features.*' => ['string', 'max:60'],
            'team_size' => ['required', 'integer', 'min:1'],
            'complexity' => ['required', 'string'],
            'preferred_platform' => ['required', 'string'],
            'development_experience' => ['required', 'string'],
            'timeline' => ['required', 'string'],
            'project_goal' => ['required', 'string', 'max:1000'],
            'scalability_needs' => ['required', 'string'],
            'security_requirements' => ['required', 'string'],
            'performance_requirements' => ['required', 'string'],
            'budget_constraints' => ['required', 'string'],
            'maintenance_expectations' => ['required', 'string'],
            'deployment_preference' => ['required', 'string'],
            'requirements_stability' => ['required', 'string'],
            'stakeholder_involvement' => ['required', 'string'],
        ];
    }
}
