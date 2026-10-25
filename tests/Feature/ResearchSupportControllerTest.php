<?php

use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

function createOwnedRecommendation(User $user): Recommendation
{
    return Recommendation::query()->create([
        'user_id' => $user->id,
        'project_name' => 'Portal Project',
        'project_type' => 'web application',
        'team_size' => 2,
        'complexity' => 'small',
        'preferred_platform' => 'web',
        'development_experience' => 'beginner',
        'timeline' => 'short',
        'project_goal' => 'Build a portal',
        'recommended_language' => 'PHP',
        'recommended_framework' => 'Laravel',
        'recommended_sdlc_model' => 'Agile',
        'confidence_score' => 70,
        'explanations' => [],
        'alternative_stacks' => [],
        'risk_analysis' => [],
        'skill_gap_analysis' => [],
        'roadmap' => [],
    ]);
}

test('guests cannot store research support', function () {
    $owner = User::factory()->create();
    $recommendation = createOwnedRecommendation($owner);

    post(route('recommendations.research-support.store', $recommendation))
        ->assertRedirect(route('login'));
});

test('authenticated users can store research support for their recommendation', function () {
    config()->set('services.ollama.api_url', 'http://ollama.test/api/chat');
    config()->set('services.ollama.model', 'llama3.2');

    Http::fake([
        'http://ollama.test/api/chat' => Http::response([
            'message' => [
                'role' => 'assistant',
                'content' => json_encode([
                    'suggested_title' => 'Generated Study',
                    'abstract' => 'Generated abstract.',
                    'introduction' => 'Generated introduction.',
                    'statement_of_problem' => 'Generated problem.',
                    'general_objective' => 'Generated objective.',
                    'specific_objectives' => ['Objective A'],
                    'scope_and_limitation' => 'Generated scope.',
                    'significance_of_study' => 'Generated significance.',
                    'suggested_methodology' => 'Generated methodology.',
                    'related_keywords' => ['laravel'],
                    'suggested_search_queries' => ['laravel research'],
                    'suggested_journals' => ['Example Journal (Indexing status: Needs manual verification)'],
                    'open_access_links' => ['https://doaj.org'],
                    'publication_recommendation' => 'Verify before submission.',
                    'scopus_note' => 'Verify indexing manually.',
                ]),
            ],
        ]),
    ]);

    $user = User::factory()->create();
    $recommendation = createOwnedRecommendation($user);

    actingAs($user)
        ->post(route('recommendations.research-support.store', $recommendation), [
            'study_title' => 'My Study',
            'study_context' => 'Capstone context',
        ])
        ->assertRedirect(route('recommendation.show', $recommendation).'#research-support')
        ->assertSessionHas('success', 'Research support generated successfully.')
        ->assertSessionMissing('warning');

    expect($recommendation->fresh()->researchSupport)->not->toBeNull()
        ->and($recommendation->fresh()->researchSupport?->study_title)->toBe('My Study');
});

test('authenticated users receive warning flash when fallback is used', function () {
    config()->set('services.ollama.api_url', '');

    $user = User::factory()->create();
    $recommendation = createOwnedRecommendation($user);

    actingAs($user)
        ->post(route('recommendations.research-support.store', $recommendation))
        ->assertRedirect(route('recommendation.show', $recommendation).'#research-support')
        ->assertSessionHas('success', 'Research support generated successfully.')
        ->assertSessionHas(
            'warning',
            'Ollama was not reachable, so a basic research support template was generated.'
        );
});

test('authenticated users cannot store research support for another users recommendation', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $recommendation = createOwnedRecommendation($owner);

    actingAs($otherUser)
        ->post(route('recommendations.research-support.store', $recommendation))
        ->assertForbidden();
});

test('existing recommendation and feedback routes remain available', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('recommendation.index'))
        ->assertOk();

    actingAs($user)
        ->get(route('recommendation.history'))
        ->assertOk();

    actingAs($user)
        ->post(route('feedback.store'), [
            'rating' => 5,
            'comment' => 'Great tool',
        ])
        ->assertRedirect();
});
