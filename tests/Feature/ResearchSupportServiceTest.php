<?php

use App\Models\Recommendation;
use App\Models\ResearchSupport;
use App\Models\User;
use App\Services\ResearchSupportService;
use Illuminate\Support\Facades\Http;

function createRecommendationForResearchSupport(User $user): Recommendation
{
    return Recommendation::query()->create([
        'user_id' => $user->id,
        'project_name' => 'StackWise Portal',
        'project_type' => 'web application',
        'selected_features' => ['authentication', 'reporting'],
        'team_size' => 3,
        'complexity' => 'medium',
        'preferred_platform' => 'web',
        'development_experience' => 'intermediate',
        'timeline' => 'medium',
        'project_goal' => 'Build a student project management portal.',
        'recommended_language' => 'PHP',
        'recommended_framework' => 'Laravel',
        'recommended_sdlc_model' => 'Agile',
        'confidence_score' => 80,
        'explanations' => [],
        'alternative_stacks' => [],
        'risk_analysis' => [
            [
                'risk_title' => 'Changing Requirements',
                'impact_level' => 'Medium',
                'explanation' => 'Scope may change during development.',
                'suggested_solution' => 'Use sprint planning and backlog grooming.',
            ],
        ],
        'skill_gap_analysis' => [],
        'roadmap' => [
            [
                'phase' => 'Phase 1',
                'task' => 'Requirement Gathering',
                'description' => 'Define scope and users.',
            ],
        ],
    ]);
}

/**
 * @return array<string, mixed>
 */
function validOllamaAcademicResearchPayload(): array
{
    return [
        'title_page' => [
            'research_title' => 'AI Suggested Study Title',
            'system_title' => 'StackWise Portal',
            'technology_stack' => 'PHP / Laravel',
            'sdlc_model' => 'Agile',
            'generated_by' => 'StackWise AI Research Support Assistant',
            'generated_date' => '2026-05-21',
        ],
        'abstract' => str_repeat('This academic abstract provides formal context for the capstone study. ', 12),
        'introduction' => "Paragraph one introduces the domain.\n\nParagraph two describes the system.\n\nParagraph three explains methodology alignment.",
        'statement_of_problem' => [
            'general' => 'Teams struggle to align scope and research documentation.',
            'specific' => [
                'How should requirements be prioritized?',
                'What methodology best fits delivery?',
                'Which risks require mitigation?',
            ],
        ],
        'objectives' => [
            'general' => 'To design and evaluate the portal system.',
            'specific' => [
                'Define requirements.',
                'Implement modules.',
                'Evaluate outcomes.',
            ],
        ],
        'scope_and_limitations' => 'Scope covers core modules. Limitations include academic timeline constraints.',
        'significance_of_study' => [
            'students' => 'Supports capstone readiness.',
            'researchers' => 'Provides case context.',
            'administrators' => 'Improves oversight.',
            'future_developers' => 'Documents maintainability decisions.',
            'organization' => 'Aligns outcomes with institutional goals.',
        ],
        'review_of_related_literature' => [
            'local_studies' => ['Review local capstone repositories.'],
            'foreign_studies' => ['Survey international agile literature.'],
            'related_systems' => ['Compare student portal systems.'],
            'technology_related' => ['Study Laravel engineering guidance.'],
        ],
        'conceptual_framework' => 'Inputs, processes, and outputs connect requirements to evaluation.',
        'methodology' => 'Agile methodology with iterative delivery and testing.',
        'technology_stack_justification' => 'PHP and Laravel support rapid web delivery for this team profile.',
        'risk_analysis_summary' => 'Monitor scope and integration risks each sprint.',
        'skill_gap_summary' => 'Upskill on testing and deployment before build phase.',
        'roadmap_summary' => 'Follow phased planning, build, test, and deploy milestones.',
        'conclusion' => 'The study is feasible with disciplined scope and verification.',
        'recommendations' => 'Maintain traceability, verify literature, and confirm journal fit.',
        'references' => [
            'apa_placeholders' => ['[Author, A. A. (Year). Title. Journal. Verify before citing.]'],
            'ieee_placeholders' => ['[A. Author, "Title," Journal, Year.]'],
            'search_guidance' => ['Use institutional databases and DOAJ.'],
        ],
        'research_discovery' => [
            'keywords' => ['laravel', 'portal'],
            'search_query_groups' => [
                ['category' => 'Google Scholar', 'queries' => ['laravel capstone portal']],
                ['category' => 'IEEE Xplore', 'queries' => ['("Laravel") AND "web application"']],
                ['category' => 'ACM Digital Library', 'queries' => ['laravel web application']],
                ['category' => 'Scopus', 'queries' => ['TITLE-ABS-KEY("Laravel")']],
                ['category' => 'DOI / Crossref', 'queries' => ['laravel capstone']],
            ],
            'journals' => [
                [
                    'name' => 'Example Journal',
                    'scope' => 'Software engineering practice.',
                    'why_it_fits' => 'Matches applied computing capstone scope.',
                    'recommended_for' => 'Undergraduate capstone with evaluation.',
                    'indexing_status' => 'Needs manual verification',
                    'publication_type' => 'Journal',
                    'fit_score' => 70,
                ],
            ],
            'open_access_links' => [
                [
                    'label' => 'DOAJ Search',
                    'url' => 'https://doaj.org/search?q=laravel',
                    'source' => 'DOAJ',
                    'description' => 'Open access journal search.',
                ],
            ],
            'publication_recommendation' => [
                'undergraduate_thesis_capstone' => 'Suitable for capstone documentation.',
                'local_conference' => 'May fit local computing conferences.',
                'software_engineering_journals' => 'Requires stronger empirical contribution.',
                'before_submission' => ['Verify citations.'],
            ],
            'scopus_verification' => [
                'intro' => 'Verify indexing manually.',
                'steps' => ['Check the Scopus Sources list.'],
            ],
        ],
    ];
}

test('research support service saves parsed ollama academic json response', function () {
    config()->set('services.ollama.api_url', 'http://ollama.test/api/chat');
    config()->set('services.ollama.api_key', '');
    config()->set('services.ollama.model', 'llama3.2');

    Http::fake([
        'http://ollama.test/api/chat' => Http::response([
            'message' => [
                'role' => 'assistant',
                'content' => json_encode(validOllamaAcademicResearchPayload()),
            ],
        ]),
    ]);

    $user = User::factory()->create();
    $recommendation = createRecommendationForResearchSupport($user);

    $result = app(ResearchSupportService::class)->generate($user, $recommendation, [
        'study_title' => 'Custom Study Title',
        'study_context' => 'Custom study context for the capstone.',
    ]);

    expect($result->usedFallback)->toBeFalse()
        ->and($result->researchSupport->suggested_title)->toBe('AI Suggested Study Title')
        ->and($result->researchSupport->title_page['research_title'])->toBe('AI Suggested Study Title')
        ->and($result->researchSupport->study_title)->toBe('Custom Study Title')
        ->and($result->researchSupport->objectives['general'])->toContain('portal')
        ->and($result->researchSupport->statement_of_problem['specific'])->toHaveCount(3)
        ->and($result->researchSupport->review_of_related_literature['local_studies'])->not->toBeEmpty()
        ->and($result->researchSupport->conclusion)->toContain('feasible')
        ->and($result->researchSupport->specific_objectives)->toBe(['Define requirements.', 'Implement modules.', 'Evaluate outcomes.'])
        ->and($result->researchSupport->raw_ai_response)->toContain('AI Suggested Study Title');
});

test('research support service still accepts legacy ollama json shape', function () {
    config()->set('services.ollama.api_url', 'http://ollama.test/api/chat');
    config()->set('services.ollama.model', 'llama3.2');

    Http::fake([
        'http://ollama.test/api/chat' => Http::response([
            'message' => [
                'role' => 'assistant',
                'content' => json_encode([
                    'suggested_title' => 'Legacy Study Title',
                    'abstract' => 'Legacy abstract.',
                    'introduction' => 'Legacy introduction.',
                    'statement_of_problem' => 'Legacy problem.',
                    'general_objective' => 'Legacy general objective.',
                    'specific_objectives' => ['Legacy objective'],
                    'scope_and_limitation' => 'Legacy scope.',
                    'significance_of_study' => 'Legacy significance.',
                    'suggested_methodology' => 'Legacy methodology.',
                    'related_keywords' => ['legacy'],
                    'suggested_search_queries' => ['legacy query'],
                    'suggested_journals' => ['Legacy Journal (Indexing status: Needs manual verification)'],
                    'open_access_links' => ['https://doaj.org'],
                    'publication_recommendation' => 'Legacy publication note.',
                    'scopus_note' => 'Legacy scopus note.',
                ]),
            ],
        ]),
    ]);

    $user = User::factory()->create();
    $recommendation = createRecommendationForResearchSupport($user);

    $result = app(ResearchSupportService::class)->generate($user, $recommendation, []);

    expect($result->usedFallback)->toBeFalse()
        ->and($result->researchSupport->suggested_title)->toBe('Legacy Study Title')
        ->and($result->researchSupport->statement_of_problem['general'])->toBe('Legacy problem.');
});

test('research support service uses complete fallback when ollama returns invalid json', function () {
    config()->set('services.ollama.api_url', 'http://ollama.test/api/chat');
    config()->set('services.ollama.api_key', '');
    config()->set('services.ollama.model', 'llama3.2');

    Http::fake([
        'http://ollama.test/api/chat' => Http::response([
            'message' => [
                'role' => 'assistant',
                'content' => 'This is not valid JSON.',
            ],
        ]),
    ]);

    $user = User::factory()->create();
    $recommendation = createRecommendationForResearchSupport($user);

    $result = app(ResearchSupportService::class)->generate($user, $recommendation, []);

    $abstractWordCount = str_word_count(strip_tags((string) $result->researchSupport->abstract));

    expect($result->usedFallback)->toBeTrue()
        ->and($result->researchSupport->suggested_title)->toContain('StackWise Portal')
        ->and($abstractWordCount)->toBeGreaterThanOrEqual(150)
        ->and($result->researchSupport->introduction)->toContain("\n\n")
        ->and($result->researchSupport->statement_of_problem['specific'])->toHaveCount(4)
        ->and($result->researchSupport->objectives['specific'])->toHaveCount(5)
        ->and($result->researchSupport->conclusion)->not->toBeEmpty()
        ->and($result->researchSupport->recommendations)->not->toBeEmpty()
        ->and($result->researchSupport->title_page['generated_by'])->toBe('StackWise AI Research Support Assistant')
        ->and($result->researchSupport->scopus_note)->toContain('Scopus Sources list')
        ->and($result->researchSupport->publication_recommendation)->toContain('AI-generated draft');
});

test('research support service uses fallback when ollama is unreachable', function () {
    config()->set('services.ollama.api_url', 'http://ollama.test/api/chat');
    config()->set('services.ollama.api_key', '');
    config()->set('services.ollama.model', 'llama3.2');

    Http::fake([
        'http://ollama.test/api/chat' => Http::response([], 500),
    ]);

    $user = User::factory()->create();
    $recommendation = createRecommendationForResearchSupport($user);

    $result = app(ResearchSupportService::class)->generate($user, $recommendation, []);

    expect($result->usedFallback)->toBeTrue()
        ->and($result->researchSupport->abstract)->toContain('StackWise Portal')
        ->and($result->researchSupport->review_of_related_literature['foreign_studies'])->not->toBeEmpty()
        ->and($result->researchSupport->scopus_note)->toContain('predatory');
});

test('research support service uses fallback when ollama is not configured', function () {
    config()->set('services.ollama.api_url', '');

    $user = User::factory()->create();
    $recommendation = createRecommendationForResearchSupport($user);

    $result = app(ResearchSupportService::class)->generate($user, $recommendation, []);

    expect($result->usedFallback)->toBeTrue()
        ->and($result->researchSupport->suggested_title)->toContain('StackWise Portal')
        ->and($result->researchSupport->suggested_journals)->each->toContain('Needs manual verification')
        ->and($result->researchSupport->risk_analysis_summary)->toContain('Changing Requirements');
});

test('research support service updates existing record for the same recommendation', function () {
    config()->set('services.ollama.api_url', '');

    $user = User::factory()->create();
    $recommendation = createRecommendationForResearchSupport($user);

    $service = app(ResearchSupportService::class);

    $first = $service->generate($user, $recommendation, [
        'study_title' => 'First Title',
    ]);

    $second = $service->generate($user, $recommendation, [
        'study_title' => 'Updated Title',
    ]);

    expect(ResearchSupport::query()->where('recommendation_id', $recommendation->id)->count())->toBe(1)
        ->and($second->researchSupport->id)->toBe($first->researchSupport->id)
        ->and($second->researchSupport->study_title)->toBe('Updated Title');
});
