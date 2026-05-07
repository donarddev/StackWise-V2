<?php

use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function basePayload(array $overrides = []): array
{
    return array_merge([
        'project_name' => 'Sample Project',
        'project_type' => 'web application',
        'selected_features' => ['crud', 'authentication'],
        'team_size' => 2,
        'complexity' => 'small',
        'preferred_platform' => 'web',
        'development_experience' => 'beginner',
        'timeline' => 'short',
        'project_goal' => 'Build a small portal for students and beginner developers.',
        'scalability_needs' => 'low',
        'security_requirements' => 'standard',
        'performance_requirements' => 'low',
        'budget_constraints' => 'low',
        'maintenance_expectations' => 'medium',
        'deployment_preference' => 'shared_hosting',
        'requirements_stability' => 'changing',
        'stakeholder_involvement' => 'medium',
    ], $overrides);
}

test('generated recommendation is internally consistent (framework matches language)', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('recommendation.generate', absolute: false), basePayload())
        ->assertRedirect();

    $recommendation = Recommendation::query()->latest('id')->firstOrFail();

    expect($recommendation->recommended_language)->not->toBeEmpty();
    expect($recommendation->recommended_framework)->not->toBeEmpty();

    $frameworkToLanguage = [
        'Laravel' => 'PHP',
        'Symfony' => 'PHP',
        'Django' => 'Python',
        'FastAPI' => 'Python',
        'Flask' => 'Python',
        'NestJS' => 'TypeScript',
        'Express' => 'TypeScript',
        'Spring Boot' => 'Java',
        'Gin' => 'Go',
        'Flutter' => 'Dart',
    ];

    expect($frameworkToLanguage[$recommendation->recommended_framework] ?? null)
        ->toBe($recommendation->recommended_language);
});

test('confidence score changes with match quality and risk', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('recommendation.generate', absolute: false), basePayload([
        'scalability_needs' => 'low',
        'security_requirements' => 'basic',
        'performance_requirements' => 'low',
    ]));

    $strong = Recommendation::query()->latest('id')->firstOrFail();

    $this->actingAs($user)->post(route('recommendation.generate', absolute: false), basePayload([
        'project_type' => 'api system',
        'selected_features' => ['real-time', 'api', 'payments'],
        'scalability_needs' => 'high',
        'security_requirements' => 'high',
        'performance_requirements' => 'high',
        'budget_constraints' => 'low',
        'maintenance_expectations' => 'high',
        'timeline' => 'short',
        'development_experience' => 'beginner',
        'requirements_stability' => 'changing',
        'stakeholder_involvement' => 'high',
    ]));

    $risky = Recommendation::query()->latest('id')->firstOrFail();

    expect($strong->confidence_score)->toBeInt()->and($strong->confidence_score)->toBeGreaterThanOrEqual(50)->toBeLessThanOrEqual(97);
    expect($risky->confidence_score)->toBeInt()->and($risky->confidence_score)->toBeGreaterThanOrEqual(50)->toBeLessThanOrEqual(97);

    expect($strong->confidence_score)->not->toBe($risky->confidence_score);
    expect($risky->risk_analysis)->toBeArray()->and($risky->risk_analysis)->not->toBeEmpty();
    expect($risky->explanations['why_not_this'] ?? [])->toBeArray();
});
