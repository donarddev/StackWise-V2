<?php

use App\Models\Recommendation;
use App\Models\ResearchSupport;
use App\Models\User;

test('academic research support components render expected structure', function () {
    $html = view('research-support.academic-layout-preview', [
        'abstract' => 'This abstract summarizes the capstone study objectives and expected contributions to software engineering education.',
        'statementGeneral' => 'Student teams struggle to align capstone scope with formal research documentation.',
        'statementSpecific' => [
            'What requirements are essential for the portal?',
            'How does Agile affect delivery quality?',
            'Which risks require mitigation during implementation?',
        ],
        'objectivesGeneral' => 'To design and evaluate the StackWise portal using Laravel and Agile practices.',
        'objectivesSpecific' => [
            'Define functional requirements.',
            'Implement core modules.',
            'Evaluate usability outcomes.',
        ],
        'keywords' => ['laravel', 'capstone'],
        'searchQueryGroups' => [
            ['category' => 'Google Scholar', 'queries' => ['laravel capstone portal']],
            ['category' => 'IEEE Xplore', 'queries' => ['("Laravel") AND "web application"']],
        ],
        'journals' => [
            [
                'name' => 'IEEE Software',
                'scope' => 'Software engineering practice and education.',
                'whyItFits' => 'Applied computing capstone studies.',
                'recommendedFor' => 'Undergraduate thesis submissions.',
                'indexingStatus' => 'Needs manual verification',
                'publicationType' => 'Journal',
                'fitScore' => 72,
            ],
        ],
        'openAccessLinks' => [
            [
                'label' => 'DOAJ Software Engineering',
                'url' => 'https://doaj.org/search?source=%22software+engineering%22',
                'source' => 'DOAJ',
                'description' => 'Directory of Open Access Journals search results.',
            ],
        ],
        'publicationRecommendation' => [
            [
                'title' => 'Undergraduate thesis / capstone',
                'body' => 'Well suited for capstone documentation.',
                'items' => [],
            ],
        ],
        'scopusVerification' => [
            'intro' => 'Verify indexing using authoritative sources.',
            'steps' => ['Check the official Scopus Sources list.'],
        ],
    ])->render();

    expect($html)
        ->toContain('A Formal Research Study on StackWise Portal')
        ->toContain('Academic manuscript workspace')
        ->toContain('Research Discovery &amp; Publication Assistant')
        ->toContain('Publication discovery')
        ->toContain('Suggested Search Queries')
        ->toContain('IEEE Xplore')
        ->toContain('Why it fits')
        ->toContain('Search links only. No automatic paper downloads.')
        ->toContain('IEEE Software')
        ->toContain('Needs manual verification')
        ->toContain('72% fit')
        ->toContain('Open search')
        ->toContain('DOAJ Software Engineering')
        ->toContain('Specific Questions')
        ->toContain('General Objective')
        ->not->toContain('bg-white"')
        ->not->toContain('bg-gray-50');
});

test('recommendation page shows academic layout when research support exists', function () {
    $user = User::factory()->create();
    $recommendation = Recommendation::query()->create([
        'user_id' => $user->id,
        'project_name' => 'Preview Guard Project',
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

    ResearchSupport::query()->create([
        'user_id' => $user->id,
        'recommendation_id' => $recommendation->id,
        'suggested_title' => 'Integrated Academic Draft',
        'abstract' => 'Abstract for integration test.',
    ]);

    \Pest\Laravel\actingAs($user)
        ->get(route('recommendation.show', $recommendation))
        ->assertOk()
        ->assertSee('Academic manuscript workspace', false)
        ->assertSee('Research Discovery &amp; Publication Assistant', false);
});

test('recommendation page hides academic workspace when research support is missing', function () {
    $user = User::factory()->create();
    $recommendation = Recommendation::query()->create([
        'user_id' => $user->id,
        'project_name' => 'Preview Guard Project',
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

    \Pest\Laravel\actingAs($user)
        ->get(route('recommendation.show', $recommendation))
        ->assertOk()
        ->assertSee('Research Support Assistant', false)
        ->assertDontSee('Academic manuscript workspace', false)
        ->assertDontSee('Research Discovery &amp; Publication Assistant', false);
});
