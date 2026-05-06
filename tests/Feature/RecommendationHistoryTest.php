<?php

use App\Models\Recommendation;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('recommendation history page loads', function () {
    $this->get(route('recommendation.history'))
        ->assertOk()
        ->assertSee('Recommendation History', false)
        ->assertSee('Decision Archive', false);
});

test('recommendation history filters by search', function () {
    Recommendation::query()->create([
        'project_name' => 'Unique Alpha Project',
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

    Recommendation::query()->create([
        'project_name' => 'Other Beta',
        'project_type' => 'ai system',
        'team_size' => 1,
        'complexity' => 'medium',
        'preferred_platform' => 'web',
        'development_experience' => 'beginner',
        'timeline' => 'medium',
        'project_goal' => 'ML demo',
        'recommended_language' => 'Python',
        'recommended_framework' => 'FastAPI',
        'recommended_sdlc_model' => 'Agile',
        'confidence_score' => 88,
        'explanations' => [],
        'alternative_stacks' => [],
        'risk_analysis' => [],
        'skill_gap_analysis' => [],
        'roadmap' => [],
    ]);

    $this->get(route('recommendation.history', ['search' => 'Unique Alpha']))
        ->assertOk()
        ->assertSee('Unique Alpha Project', false)
        ->assertDontSee('Other Beta', false);
});
