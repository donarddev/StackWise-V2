<?php

use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('generating a recommendation redirects to the newly created recommendation detail page', function () {
    $user = User::factory()->create();

    $payload = [
        'project_name' => 'Enrollment Portal',
        'project_type' => 'web application',
        'selected_features' => ['crud', 'authentication', 'api'],
        'team_size' => 3,
        'complexity' => 'small',
        'preferred_platform' => 'web',
        'development_experience' => 'beginner',
        'timeline' => 'short',
        'project_goal' => 'Build a simple enrollment portal for students.',
        'scalability_needs' => 'low',
        'security_requirements' => 'standard',
        'performance_requirements' => 'medium',
        'budget_constraints' => 'low',
        'maintenance_expectations' => 'medium',
        'deployment_preference' => 'shared_hosting',
        'requirements_stability' => 'changing',
        'stakeholder_involvement' => 'medium',
    ];

    $response = $this->actingAs($user)->post(route('recommendation.generate', absolute: false), $payload);

    $recommendation = Recommendation::query()->latest('id')->firstOrFail();

    $response
        ->assertRedirect(route('recommendation.show', $recommendation, absolute: false))
        ->assertSessionHas('success', 'Recommendation generated successfully.');

    expect($recommendation->user_id)->toBe($user->id);
});

test('users cannot view another users recommendation details when user_id is present', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    $recommendation = Recommendation::query()->create([
        'user_id' => $owner->id,
        'project_name' => 'Owner Project',
        'project_type' => 'web application',
        'team_size' => 2,
        'complexity' => 'small',
        'preferred_platform' => 'web',
        'development_experience' => 'beginner',
        'timeline' => 'short',
        'project_goal' => 'Build a portal',
        'recommended_language' => 'PHP',
        'recommended_framework' => 'Laravel',
        'recommended_sdlc_model' => 'Waterfall',
        'confidence_score' => 72,
        'explanations' => [],
        'alternative_stacks' => [],
        'risk_analysis' => [],
        'skill_gap_analysis' => [],
        'roadmap' => [],
    ]);

    $this->actingAs($other)
        ->get(route('recommendation.show', $recommendation, absolute: false))
        ->assertForbidden();
});
