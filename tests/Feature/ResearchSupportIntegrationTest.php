<?php

use App\Models\Recommendation;
use App\Models\ResearchSupport;
use App\Models\User;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\actingAs;

function integrationRecommendation(User $user, array $overrides = []): Recommendation
{
    return Recommendation::query()->create(array_merge([
        'user_id' => $user->id,
        'project_name' => 'StackWise Capstone',
        'project_type' => 'web application',
        'selected_features' => ['authentication', 'reporting'],
        'team_size' => 3,
        'complexity' => 'medium',
        'preferred_platform' => 'web',
        'development_experience' => 'intermediate',
        'timeline' => 'medium',
        'project_goal' => 'Build a capstone project management portal.',
        'recommended_language' => 'PHP',
        'recommended_framework' => 'Laravel',
        'recommended_sdlc_model' => 'Agile',
        'confidence_score' => 82,
        'explanations' => [],
        'alternative_stacks' => [],
        'risk_analysis' => [
            [
                'risk_title' => 'Scope Creep',
                'impact_level' => 'Medium',
                'explanation' => 'Features may expand beyond timeline.',
                'suggested_solution' => 'Prioritize MVP features.',
            ],
        ],
        'skill_gap_analysis' => [],
        'roadmap' => [
            ['phase' => 'Phase 1', 'task' => 'Planning', 'description' => 'Define requirements.'],
        ],
    ], $overrides));
}

function researchSupportShowUrl(Recommendation $recommendation): string
{
    return route('recommendation.show', $recommendation).'#research-support';
}

test('scenario 1 recommendation without research support shows section before feedback', function () {
    $user = User::factory()->create();
    $recommendation = integrationRecommendation($user);

    $response = actingAs($user)->get(route('recommendation.show', $recommendation));

    $response
        ->assertOk()
        ->assertSee('Research Support Assistant', false)
        ->assertSee('No research support generated yet.', false)
        ->assertSee('Generate Research Support', false)
        ->assertSee('name="study_title"', false)
        ->assertSee('name="study_context"', false);

    $content = $response->getContent();
    $researchPosition = strpos($content, 'id="research-support"');
    $feedbackListPosition = strpos($content, 'Saved feedback entries linked to this recommendation');
    $feedbackFormPosition = strpos($content, 'id="feedback"');

    expect($researchPosition)->not->toBeFalse()
        ->and($feedbackListPosition)->not->toBeFalse()
        ->and($feedbackFormPosition)->not->toBeFalse()
        ->and($researchPosition)->toBeLessThan($feedbackListPosition)
        ->and($researchPosition)->toBeLessThan($feedbackFormPosition);
});

test('scenario 2 generate with empty study fields saves record and shows output', function () {
    config()->set('services.ollama.api_url', '');

    $user = User::factory()->create();
    $recommendation = integrationRecommendation($user);

    actingAs($user)
        ->post(route('recommendations.research-support.store', $recommendation), [
            'study_title' => '',
            'study_context' => '',
        ])
        ->assertRedirect(researchSupportShowUrl($recommendation))
        ->assertSessionHas('success', 'Research support generated successfully.')
        ->assertSessionHas('warning');

    $researchSupport = $recommendation->fresh()->researchSupport;

    expect($researchSupport)->not->toBeNull()
        ->and($researchSupport->study_title)->toBeIn([null, ''])
        ->and($researchSupport->study_context)->toBeIn([null, ''])
        ->and($researchSupport->suggested_title)->toContain('StackWise Capstone');

    actingAs($user)
        ->get(route('recommendation.show', $recommendation))
        ->assertOk()
        ->assertSee('Section A — Research Paper Workspace', false)
        ->assertSee('Regenerate Research Support', false)
        ->assertSee('StackWise Capstone', false)
        ->assertDontSee('No research support generated yet.', false);
});

test('scenario 3 generate with custom study fields saves and reflects context in output', function () {
    config()->set('services.ollama.api_url', '');

    $user = User::factory()->create();
    $recommendation = integrationRecommendation($user);

    actingAs($user)
        ->post(route('recommendations.research-support.store', $recommendation), [
            'study_title' => 'Secure Portal Research',
            'study_context' => 'Focus on role-based access control and audit logging.',
        ])
        ->assertRedirect(researchSupportShowUrl($recommendation));

    $researchSupport = $recommendation->fresh()->researchSupport;

    expect($researchSupport?->study_title)->toBe('Secure Portal Research')
        ->and($researchSupport?->suggested_title)->toBe('Secure Portal Research')
        ->and($researchSupport?->abstract)->toContain('role-based access control and audit logging');

    actingAs($user)
        ->get(route('recommendation.show', $recommendation))
        ->assertOk()
        ->assertSee('Secure Portal Research', false)
        ->assertSee('role-based access control and audit logging', false);
});

test('scenario 4 regenerate updates existing record without duplicates', function () {
    config()->set('services.ollama.api_url', '');

    $user = User::factory()->create();
    $recommendation = integrationRecommendation($user);

    actingAs($user)->post(route('recommendations.research-support.store', $recommendation), [
        'study_title' => 'First Draft',
    ]);

    $first = $recommendation->fresh()->researchSupport;
    expect($first)->not->toBeNull();

    actingAs($user)->post(route('recommendations.research-support.store', $recommendation), [
        'study_title' => 'Second Draft',
    ]);

    $second = $recommendation->fresh()->researchSupport;

    expect(ResearchSupport::query()->where('recommendation_id', $recommendation->id)->count())->toBe(1)
        ->and($second?->id)->toBe($first?->id)
        ->and($second?->study_title)->toBe('Second Draft');
});

test('scenario 5 user cannot generate research support for another users recommendation', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $recommendation = integrationRecommendation($owner);

    actingAs($intruder)
        ->post(route('recommendations.research-support.store', $recommendation))
        ->assertForbidden();

    expect($recommendation->fresh()->researchSupport)->toBeNull();
});

test('scenario 6 ollama offline or invalid url uses fallback without crashing', function () {
    config()->set('services.ollama.api_url', 'http://invalid-ollama.test/api/chat');
    config()->set('services.ollama.model', 'llama3.2');

    Http::fake([
        'http://invalid-ollama.test/api/chat' => Http::response([], 500),
    ]);

    $user = User::factory()->create();
    $recommendation = integrationRecommendation($user);

    actingAs($user)
        ->post(route('recommendations.research-support.store', $recommendation))
        ->assertRedirect(researchSupportShowUrl($recommendation))
        ->assertSessionHas('warning', 'Ollama was not reachable, so a basic research support template was generated.');

    actingAs($user)
        ->get(route('recommendation.show', $recommendation))
        ->assertOk()
        ->assertSee('Research Discovery &amp; Publication Assistant', false)
        ->assertSee('Scopus Sources list', false)
        ->assertSee('Search links only. No automatic paper downloads.', false)
        ->assertSee('Research Support Assistant', false);
});

test('scenario 7 research support ui uses dark theme classes and preserves existing sections', function () {
    $user = User::factory()->create();
    $recommendation = integrationRecommendation($user);

    ResearchSupport::query()->create([
        'user_id' => $user->id,
        'recommendation_id' => $recommendation->id,
        'suggested_title' => 'Dark Theme Draft',
        'abstract' => 'Abstract content.',
        'related_keywords' => ['php'],
    ]);

    $response = actingAs($user)->get(route('recommendation.show', $recommendation));

    $response
        ->assertOk()
        ->assertSee('id="research-support"', false)
        ->assertSee('bg-slate-900/70', false)
        ->assertSee('bg-white/5', false)
        ->assertSee('Project Summary', false)
        ->assertSee('Saved Recommendation', false)
        ->assertSee('Alternative Technology Stacks', false)
        ->assertSee('Suggested Project Roadmap', false)
        ->assertSee('Feedback', false)
        ->assertSee('Submit Feedback', false);

    $sectionStart = strpos($response->getContent(), 'id="research-support"');
    $sectionSnippet = substr($response->getContent(), $sectionStart, 4000);

    expect($sectionSnippet)->not->toContain('bg-white"')
        ->and($sectionSnippet)->not->toContain('bg-gray-50')
        ->and($sectionSnippet)->not->toContain('bg-slate-50');
});
