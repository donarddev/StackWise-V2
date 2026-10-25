<?php

use App\Models\Recommendation;
use App\Models\ResearchSupport;
use App\Models\User;
use App\Services\ResearchSupportViewDataService;

test('research support view data service builds paper and discovery payloads', function () {
    $user = User::factory()->create();
    $recommendation = Recommendation::query()->create([
        'user_id' => $user->id,
        'project_name' => 'Portal',
        'project_type' => 'web application',
        'team_size' => 2,
        'complexity' => 'small',
        'preferred_platform' => 'web',
        'development_experience' => 'beginner',
        'timeline' => 'short',
        'project_goal' => 'Build portal',
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

    $researchSupport = ResearchSupport::query()->create([
        'user_id' => $user->id,
        'recommendation_id' => $recommendation->id,
        'suggested_title' => 'Portal Study',
        'title_page' => [
            'research_title' => 'Portal Study',
            'system_title' => 'Portal',
            'technology_stack' => 'PHP / Laravel',
            'sdlc_model' => 'Agile',
            'generated_by' => 'StackWise AI Research Support Assistant',
            'generated_date' => '2026-05-21',
        ],
        'abstract' => 'Abstract text.',
        'introduction' => "Paragraph one.\n\nParagraph two.",
        'statement_of_problem' => [
            'general' => 'General problem.',
            'specific' => ['Question one?', 'Question two?'],
        ],
        'objectives' => [
            'general' => 'General objective.',
            'specific' => ['Objective A'],
        ],
        'significance_of_study' => [
            'students' => 'Helps students.',
            'researchers' => 'Helps researchers.',
        ],
        'review_of_related_literature' => [
            'local_studies' => ['Local direction.'],
            'foreign_studies' => ['Foreign direction.'],
        ],
        'references' => [
            'apa_placeholders' => ['[Placeholder APA]'],
            'search_guidance' => ['Search IEEE Xplore.'],
        ],
        'research_discovery' => [
            'keywords' => ['portal'],
            'search_query_groups' => [
                ['category' => 'Google Scholar', 'queries' => ['portal laravel']],
            ],
            'journals' => [
                [
                    'name' => 'IEEE Software',
                    'scope' => 'Software engineering.',
                    'why_it_fits' => 'Applied computing fit.',
                    'recommended_for' => 'Capstone studies.',
                    'indexing_status' => 'Needs manual verification',
                    'publication_type' => 'Journal',
                    'fit_score' => 72,
                ],
            ],
            'open_access_links' => [
                [
                    'label' => 'DOAJ',
                    'url' => 'https://doaj.org',
                    'source' => 'DOAJ',
                    'description' => 'Search open journals.',
                ],
            ],
            'publication_recommendation' => [
                'undergraduate_thesis_capstone' => 'Good capstone fit.',
                'before_submission' => ['Check ethics approval.'],
            ],
            'scopus_verification' => [
                'intro' => 'Verify Scopus manually.',
                'steps' => ['Check ISSN on publisher site.'],
            ],
        ],
    ]);

    $viewData = app(ResearchSupportViewDataService::class)->forDisplay($researchSupport);

    expect($viewData)->not->toBeNull()
        ->and($viewData['paper']['workspaceTitle'])->toBe('Portal Study')
        ->and($viewData['paper']['statementOfProblem']['specific'])->toHaveCount(2)
        ->and($viewData['paper']['significanceEntries'])->toHaveCount(2)
        ->and($viewData['paper']['rrlGroups'])->toHaveCount(2)
        ->and($viewData['discovery']['keywords'])->toContain('portal', 'PHP')
        ->and($viewData['discovery']['searchQueryGroups'][0]['category'])->toBe('Google Scholar')
        ->and($viewData['discovery']['journals'][0]['name'])->toBe('IEEE Software')
        ->and($viewData['discovery']['journals'][0]['whyItFits'])->toBe('Applied computing fit.')
        ->and($viewData['discovery']['journals'][0]['indexingStatus'])->toBe('Needs manual verification')
        ->and($viewData['discovery']['publicationRecommendation'])->not->toBeEmpty();
});

test('research support view data service normalizes legacy string fields', function () {
    $user = User::factory()->create();
    $recommendation = Recommendation::query()->create([
        'user_id' => $user->id,
        'project_name' => 'Legacy Portal',
        'project_type' => 'web application',
        'team_size' => 2,
        'complexity' => 'small',
        'preferred_platform' => 'web',
        'development_experience' => 'beginner',
        'timeline' => 'short',
        'project_goal' => 'Build portal',
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

    $researchSupport = ResearchSupport::query()->create([
        'user_id' => $user->id,
        'recommendation_id' => $recommendation->id,
        'suggested_title' => 'Legacy Study',
        'statement_of_problem' => ['content' => 'Legacy migrated problem statement.'],
        'general_objective' => 'Legacy general objective.',
        'specific_objectives' => ['Legacy specific objective.'],
        'significance_of_study' => 'Legacy significance paragraph.',
        'suggested_journals' => ['Legacy Journal (indexing_status: Needs manual verification)'],
    ]);

    $viewData = app(ResearchSupportViewDataService::class)->forDisplay($researchSupport);

    expect($viewData['paper']['statementOfProblem']['general'])->toBe('Legacy migrated problem statement.')
        ->and($viewData['paper']['objectives']['general'])->toBe('Legacy general objective.')
        ->and($viewData['paper']['significanceEntries'][0]['description'])->toBe('Legacy significance paragraph.')
        ->and($viewData['discovery']['journals'][0]['name'])->toBe('Legacy Journal');
});
