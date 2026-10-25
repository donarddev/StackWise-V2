<?php

use App\Models\Recommendation;
use App\Models\ResearchSupport;
use App\Models\User;
use App\Services\ResearchSupportService;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

function finalValidationRecommendation(User $user, array $overrides = []): Recommendation
{
    return Recommendation::query()->create(array_merge([
        'user_id' => $user->id,
        'project_name' => 'Final Validation Portal',
        'project_type' => 'web application',
        'selected_features' => ['authentication', 'dashboard'],
        'team_size' => 3,
        'complexity' => 'medium',
        'preferred_platform' => 'web',
        'development_experience' => 'intermediate',
        'timeline' => 'medium',
        'project_goal' => 'Build a capstone portal for project tracking.',
        'recommended_language' => 'PHP',
        'recommended_framework' => 'Laravel',
        'recommended_sdlc_model' => 'Agile',
        'confidence_score' => 85,
        'explanations' => [],
        'alternative_stacks' => [],
        'risk_analysis' => [],
        'skill_gap_analysis' => [],
        'roadmap' => [],
    ], $overrides));
}

/**
 * @return array<string, mixed>
 */
function fullAcademicOllamaPayload(): array
{
    $abstract = str_repeat(
        'This formal abstract explains the capstone portal study, methodology, evaluation plan, and expected academic contribution for software engineering students. ',
        12,
    );

    return [
        'title_page' => [
            'research_title' => 'Final Validation Portal Research',
            'system_title' => 'Final Validation Portal',
            'technology_stack' => 'PHP / Laravel',
            'sdlc_model' => 'Agile',
            'generated_by' => 'StackWise AI Research Support Assistant',
            'generated_date' => '2026-05-21',
        ],
        'abstract' => trim($abstract),
        'introduction' => "Paragraph one introduces the domain.\n\nParagraph two describes the system context.\n\nParagraph three outlines methodology alignment.",
        'statement_of_problem' => [
            'general' => 'Students lack structured academic documentation for capstone delivery.',
            'specific' => [
                'How should requirements be validated?',
                'What methodology best fits delivery?',
                'Which risks require mitigation?',
            ],
        ],
        'objectives' => [
            'general' => 'To design and evaluate the portal.',
            'specific' => ['Define requirements.', 'Implement modules.', 'Evaluate outcomes.'],
        ],
        'scope_and_limitations' => 'Academic scope with timeline constraints.',
        'significance_of_study' => [
            'students' => 'Improves capstone readiness.',
            'researchers' => 'Provides case context.',
            'administrators' => 'Supports oversight.',
            'future_developers' => 'Guides maintenance.',
            'organization' => 'Aligns outcomes.',
        ],
        'review_of_related_literature' => [
            'local_studies' => ['Review local capstone repositories for portal systems.'],
            'foreign_studies' => ['Survey international agile literature.'],
            'related_systems' => ['Compare student portal feature sets.'],
            'technology_related' => ['Study Laravel engineering guidance.'],
        ],
        'conceptual_framework' => 'Inputs, processes, and outputs are linked.',
        'methodology' => 'Agile SDLC with iterative testing.',
        'technology_stack_justification' => 'Laravel supports rapid secure delivery.',
        'risk_analysis_summary' => 'Monitor scope and schedule risks.',
        'skill_gap_summary' => 'Upskill on testing practices.',
        'roadmap_summary' => 'Plan, build, test, deploy.',
        'conclusion' => 'The study is academically feasible.',
        'recommendations' => 'Maintain traceability and verify sources.',
        'references' => [
            'apa_placeholders' => ['[Placeholder — verify before citing.]'],
            'ieee_placeholders' => [],
            'search_guidance' => ['Use institutional databases.'],
        ],
        'research_discovery' => [
            'keywords' => ['portal', 'laravel'],
            'search_query_groups' => [
                ['category' => 'Google Scholar', 'queries' => ['student portal laravel']],
                ['category' => 'IEEE Xplore', 'queries' => ['("Laravel") AND "web application"']],
                ['category' => 'ACM Digital Library', 'queries' => ['capstone portal']],
                ['category' => 'Scopus', 'queries' => ['TITLE-ABS-KEY("portal")']],
                ['category' => 'DOI / Crossref', 'queries' => ['portal capstone']],
            ],
            'journals' => [
                [
                    'name' => 'IEEE Software',
                    'scope' => 'Software engineering practice.',
                    'why_it_fits' => 'Applied computing studies.',
                    'recommended_for' => 'Capstone submissions.',
                    'indexing_status' => 'Needs manual verification',
                    'publication_type' => 'Journal',
                    'fit_score' => 75,
                ],
            ],
            'open_access_links' => [
                [
                    'label' => 'Google Scholar',
                    'url' => 'https://scholar.google.com/scholar?q=portal',
                    'source' => 'Google Scholar',
                    'description' => 'Search only.',
                ],
            ],
            'publication_recommendation' => [
                'undergraduate_thesis_capstone' => 'Suitable for thesis.',
                'local_conference' => 'May fit local conferences.',
                'software_engineering_journals' => 'Requires stronger evaluation.',
                'before_submission' => ['Verify citations.'],
            ],
            'scopus_verification' => [
                'intro' => 'Verify indexing using authoritative sources.',
                'steps' => ['Check Scopus Sources list.', 'Confirm ISSN.'],
            ],
        ],
    ];
}

test('final validation 1 empty recommendation shows empty state and feedback below research support', function () {
    $user = User::factory()->create();
    $recommendation = finalValidationRecommendation($user);

    $response = actingAs($user)->get(route('recommendation.show', $recommendation));

    $content = $response
        ->assertOk()
        ->assertSee('Research Support Assistant', false)
        ->assertSee('No research support generated yet.', false)
        ->assertSee('Generate Research Support', false)
        ->assertDontSee('Section A — Research Paper Workspace', false)
        ->getContent();

    $researchPos = strpos($content, 'id="research-support"');
    $feedbackPos = strpos($content, 'id="feedback"');

    expect($researchPos)->not->toBeFalse()
        ->and($feedbackPos)->not->toBeFalse()
        ->and($researchPos)->toBeLessThan($feedbackPos);
});

test('final validation 2 generate with ollama saves full academic output and displays paper workspace', function () {
    config()->set('services.ollama.api_url', 'http://ollama.test/api/chat');
    config()->set('services.ollama.model', 'llama3.2');

    Http::fake([
        'http://ollama.test/api/chat' => Http::response([
            'message' => [
                'role' => 'assistant',
                'content' => json_encode(fullAcademicOllamaPayload()),
            ],
        ]),
    ]);

    $user = User::factory()->create();
    $recommendation = finalValidationRecommendation($user);

    actingAs($user)
        ->post(route('recommendations.research-support.store', $recommendation), [
            'study_title' => 'Final Validation Portal Research',
            'study_context' => 'Focus on authentication and dashboard analytics.',
        ])
        ->assertRedirect(route('recommendation.show', $recommendation).'#research-support')
        ->assertSessionHas('success')
        ->assertSessionMissing('warning');

    $researchSupport = $recommendation->fresh()->researchSupport;

    expect($researchSupport)->not->toBeNull()
        ->and($researchSupport->study_title)->toBe('Final Validation Portal Research')
        ->and($researchSupport->title_page['research_title'])->toBe('Final Validation Portal Research')
        ->and(str_word_count(strip_tags((string) $researchSupport->abstract)))->toBeGreaterThanOrEqual(150)
        ->and($researchSupport->introduction)->toContain("\n\n");

    actingAs($user)
        ->get(route('recommendation.show', $recommendation))
        ->assertOk()
        ->assertSee('Academic manuscript workspace', false)
        ->assertSee('Section A — Research Paper Workspace', false)
        ->assertSee('Section B — Research Discovery', false)
        ->assertSee('Final Validation Portal Research', false);
});

test('final validation 3 regenerate updates same research support record', function () {
    config()->set('services.ollama.api_url', '');

    $user = User::factory()->create();
    $recommendation = finalValidationRecommendation($user);

    actingAs($user)->post(route('recommendations.research-support.store', $recommendation), [
        'study_title' => 'Draft One',
    ]);

    $firstId = $recommendation->fresh()->researchSupport?->id;

    actingAs($user)->post(route('recommendations.research-support.store', $recommendation), [
        'study_title' => 'Draft Two',
    ]);

    expect(ResearchSupport::query()->where('recommendation_id', $recommendation->id)->count())->toBe(1)
        ->and($recommendation->fresh()->researchSupport?->id)->toBe($firstId)
        ->and($recommendation->fresh()->researchSupport?->study_title)->toBe('Draft Two');
});

test('final validation 4 ollama offline uses fallback without crashing', function () {
    config()->set('services.ollama.api_url', 'http://offline-ollama.test/api/chat');
    config()->set('services.ollama.model', 'llama3.2');

    Http::fake([
        'http://offline-ollama.test/api/chat' => Http::response([], 500),
    ]);

    $user = User::factory()->create();
    $recommendation = finalValidationRecommendation($user);

    actingAs($user)
        ->post(route('recommendations.research-support.store', $recommendation))
        ->assertRedirect(route('recommendation.show', $recommendation).'#research-support')
        ->assertSessionHas('warning');

    $researchSupport = $recommendation->fresh()->researchSupport;

    expect($researchSupport)->not->toBeNull()
        ->and($researchSupport->conclusion)->not->toBeEmpty()
        ->and($researchSupport->statement_of_problem['specific'] ?? [])->not->toBeEmpty();

    actingAs($user)
        ->get(route('recommendation.show', $recommendation))
        ->assertOk()
        ->assertSee('Academic manuscript workspace', false);
});

test('final validation 5 layout uses dark containers and separates paper from discovery', function () {
    config()->set('services.ollama.api_url', '');

    $user = User::factory()->create();
    $recommendation = finalValidationRecommendation($user);

    actingAs($user)->post(route('recommendations.research-support.store', $recommendation));

    $content = actingAs($user)
        ->get(route('recommendation.show', $recommendation))
        ->assertOk()
        ->assertSee('Academic manuscript workspace', false)
        ->assertSee('Research Discovery &amp; Publication Assistant', false)
        ->assertSee('Project Summary', false)
        ->assertSee('Feedback', false)
        ->getContent();

    $paperPos = strpos($content, 'Academic manuscript workspace');
    $discoveryPos = strpos($content, 'Research Discovery &amp; Publication Assistant');
    $feedbackPos = strpos($content, 'id="feedback"');

    expect($paperPos)->not->toBeFalse()
        ->and($discoveryPos)->not->toBeFalse()
        ->and($feedbackPos)->not->toBeFalse()
        ->and($paperPos)->toBeLessThan($discoveryPos)
        ->and($discoveryPos)->toBeLessThan($feedbackPos)
        ->and($content)->not->toContain('bg-white"')
        ->not->toContain('bg-gray-50');
});

test('final validation 6 content quality for fallback generated research support', function () {
    $user = User::factory()->create();
    $recommendation = finalValidationRecommendation($user);

    $result = app(ResearchSupportService::class)->generate($user, $recommendation, [
        'study_title' => 'Quality Check Study',
    ]);

    $support = $result->researchSupport->fresh();
    $discovery = is_array($support->research_discovery) ? $support->research_discovery : [];
    $rrl = is_array($support->review_of_related_literature) ? $support->review_of_related_literature : [];

    expect(str_word_count(strip_tags((string) $support->abstract)))->toBeGreaterThanOrEqual(150)
        ->and(substr_count((string) $support->introduction, "\n\n"))->toBeGreaterThanOrEqual(2)
        ->and($support->statement_of_problem['general'] ?? '')->not->toBe('')
        ->and(count($support->statement_of_problem['specific'] ?? []))->toBeGreaterThanOrEqual(3)
        ->and($support->objectives['general'] ?? $support->general_objective)->not->toBe('')
        ->and(collect($rrl['local_studies'] ?? [])->join(' '))->not->toContain('DOI: 10.')
        ->and(collect($discovery['journals'] ?? [])->first()['indexing_status'] ?? '')->toBe('Needs manual verification')
        ->and(collect($discovery['open_access_links'] ?? [])->pluck('url')->join(' '))->toContain('scholar.google.com');
});

test('final validation 7 authorization blocks other users from show and generate', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $recommendation = finalValidationRecommendation($owner);

    actingAs($intruder)
        ->get(route('recommendation.show', $recommendation))
        ->assertForbidden();

    actingAs($intruder)
        ->post(route('recommendations.research-support.store', $recommendation))
        ->assertForbidden();

    expect($recommendation->fresh()->researchSupport)->toBeNull();
});

test('final validation guest cannot access recommendation show', function () {
    $user = User::factory()->create();
    $recommendation = finalValidationRecommendation($user);

    get(route('recommendation.show', $recommendation))
        ->assertRedirect(route('login'));
});
