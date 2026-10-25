<?php

use App\Models\Recommendation;
use App\Models\User;
use App\Services\ResearchDiscoveryBuilder;

test('research discovery builder creates categorized queries and safe open access links', function () {
    $user = User::factory()->create();
    $recommendation = Recommendation::query()->create([
        'user_id' => $user->id,
        'project_name' => 'StackWise Portal',
        'project_type' => 'web application',
        'team_size' => 2,
        'complexity' => 'small',
        'preferred_platform' => 'web',
        'development_experience' => 'beginner',
        'timeline' => 'short',
        'project_goal' => 'Build a portal.',
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

    $discovery = app(ResearchDiscoveryBuilder::class)->build($recommendation, 'Portal Study', null);

    $categories = array_column($discovery['search_query_groups'], 'category');

    expect($categories)->toContain('Google Scholar', 'IEEE Xplore', 'ACM Digital Library', 'Scopus', 'DOI / Crossref')
        ->and($discovery['journals'][0])->toHaveKeys([
            'name', 'scope', 'why_it_fits', 'recommended_for', 'indexing_status', 'publication_type', 'fit_score',
        ])
        ->and($discovery['journals'][0]['indexing_status'])->toBe('Needs manual verification')
        ->and(collect($discovery['open_access_links'])->pluck('url')->join(' '))
        ->toContain('scholar.google.com')
        ->toContain('doaj.org')
        ->toContain('openalex.org')
        ->toContain('crossref.org')
        ->toContain('semanticscholar.org')
        ->toContain('core.ac.uk')
        ->and($discovery['publication_recommendation'])->toHaveKeys([
            'undergraduate_thesis_capstone',
            'local_conference',
            'software_engineering_journals',
            'before_submission',
        ])
        ->and($discovery['scopus_verification']['steps'])->not->toBeEmpty();
});

test('research discovery builder normalizes legacy flat discovery fields', function () {
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

    $normalized = app(ResearchDiscoveryBuilder::class)->normalize([
        'keywords' => ['portal'],
        'search_queries' => ['legacy query'],
        'journals' => ['Legacy Journal (indexing_status: Needs manual verification)'],
        'open_access_links' => ['https://doaj.org'],
        'publication_recommendation' => 'Legacy publication note.',
        'scopus_note' => 'Legacy scopus note.',
    ], $recommendation);

    expect($normalized['search_query_groups'])->not->toBeEmpty()
        ->and($normalized['journals'][0]['name'])->toBe('Legacy Journal')
        ->and($normalized['open_access_links'])->not->toBeEmpty()
        ->and($normalized['scopus_verification']['intro'])->toBe('Legacy scopus note.');
});
