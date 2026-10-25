<?php

use App\Models\Recommendation;
use App\Models\ResearchSupport;
use App\Models\User;

use function Pest\Laravel\actingAs;

function createRecommendationForDetailsPage(User $user): Recommendation
{
    return Recommendation::query()->create([
        'user_id' => $user->id,
        'project_name' => 'Capstone Portal',
        'project_type' => 'web application',
        'team_size' => 2,
        'complexity' => 'small',
        'preferred_platform' => 'web',
        'development_experience' => 'beginner',
        'timeline' => 'short',
        'project_goal' => 'Build a student portal.',
        'recommended_language' => 'PHP',
        'recommended_framework' => 'Laravel',
        'recommended_sdlc_model' => 'Agile',
        'confidence_score' => 75,
        'explanations' => [],
        'alternative_stacks' => [],
        'risk_analysis' => [],
        'skill_gap_analysis' => [],
        'roadmap' => [
            ['phase' => 'Phase 1', 'task' => 'Planning', 'description' => 'Define scope.'],
        ],
    ]);
}

test('recommendation details shows research support empty state and generate form', function () {
    $user = User::factory()->create();
    $recommendation = createRecommendationForDetailsPage($user);

    actingAs($user)
        ->get(route('recommendation.show', $recommendation))
        ->assertOk()
        ->assertSee('Research Support Assistant', false)
        ->assertSee('No research support generated yet.', false)
        ->assertSee('Generate Research Support', false)
        ->assertSee(route('recommendations.research-support.store', $recommendation), false)
        ->assertSee('This AI-generated research support output is a draft', false)
        ->assertDontSee('Regenerate Research Support', false)
        ->assertDontSee('Section A — Research Paper Workspace', false)
        ->assertDontSee('Research Discovery &amp; Publication Assistant', false);
});

test('recommendation details shows academic paper workspace and discovery assistant', function () {
    $user = User::factory()->create();
    $recommendation = createRecommendationForDetailsPage($user);

    ResearchSupport::query()->create([
        'user_id' => $user->id,
        'recommendation_id' => $recommendation->id,
        'suggested_title' => 'Portal Research Study',
        'title_page' => [
            'research_title' => 'Portal Research Study',
            'system_title' => 'Capstone Portal',
            'technology_stack' => 'PHP / Laravel',
            'sdlc_model' => 'Agile',
            'generated_by' => 'StackWise AI Research Support Assistant',
            'generated_date' => '2026-05-21',
        ],
        'abstract' => 'This study examines portal development.',
        'introduction' => "First paragraph.\n\nSecond paragraph.",
        'statement_of_problem' => [
            'general' => 'Students need better portal planning.',
            'specific' => ['How should modules be prioritized?', 'What risks affect delivery?'],
        ],
        'objectives' => [
            'general' => 'Build and evaluate the portal.',
            'specific' => ['Implement authentication', 'Evaluate usability'],
        ],
        'scope_and_limitation' => 'Scope details.',
        'significance_of_study' => [
            'students' => 'Supports capstone readiness.',
            'researchers' => 'Provides a case study context.',
        ],
        'suggested_methodology' => 'Agile methodology.',
        'related_keywords' => ['portal', 'laravel'],
        'suggested_search_queries' => ['student portal laravel'],
        'suggested_journals' => [
            ['name' => 'Example Journal', 'indexing_status' => 'Needs manual verification'],
        ],
        'open_access_links' => ['https://doaj.org'],
        'publication_recommendation' => 'Verify venue fit before submission.',
        'scopus_note' => 'Verify indexing manually.',
    ]);

    $response = actingAs($user)
        ->get(route('recommendation.show', $recommendation))
        ->assertOk()
        ->assertSee('Regenerate Research Support', false)
        ->assertSee('AI-generated draft', false)
        ->assertSee('Section A — Research Paper Workspace', false)
        ->assertSee('Section B — Research Discovery', false)
        ->assertSee('Academic manuscript workspace', false)
        ->assertSee('Research Discovery &amp; Publication Assistant', false)
        ->assertSee('Portal Research Study', false)
        ->assertSee('Related Keywords', false)
        ->assertSee('Suggested Search Queries', false)
        ->assertSee('Needs manual verification', false)
        ->assertSee('https://doaj.org', false)
        ->assertSee('Search links only. No automatic paper downloads.', false)
        ->assertSee('Google Scholar', false)
        ->assertSee('IEEE Xplore', false)
        ->assertSee('Why it fits', false)
        ->assertDontSee('No research support generated yet.', false)
        ->assertDontSee('Generated Research Support', false)
        ->assertDontSee('Suggested Research Title', false);

    $content = $response->getContent();
    $roadmapPosition = strpos($content, 'Suggested Project Roadmap');
    $researchPosition = strpos($content, 'Research Support Assistant');
    $feedbackPosition = strpos($content, 'Saved feedback entries linked to this recommendation');

    expect($roadmapPosition)->not->toBeFalse()
        ->and($researchPosition)->not->toBeFalse()
        ->and($feedbackPosition)->not->toBeFalse()
        ->and($roadmapPosition)->toBeLessThan($researchPosition)
        ->and($researchPosition)->toBeLessThan($feedbackPosition);
});

test('recommendation details keeps existing sections intact', function () {
    $user = User::factory()->create();
    $recommendation = createRecommendationForDetailsPage($user);

    actingAs($user)
        ->get(route('recommendation.show', $recommendation))
        ->assertOk()
        ->assertSee('Project Summary', false)
        ->assertSee('Saved Recommendation', false)
        ->assertSee('Alternative Technology Stacks', false)
        ->assertSee('Why Not This', false)
        ->assertSee('Risk Analysis', false)
        ->assertSee('Skill Gap Analysis', false)
        ->assertSee('Suggested Project Roadmap', false)
        ->assertSee('Feedback', false);
});

test('recommendation details enriches minimal discovery data for display', function () {
    $user = User::factory()->create();
    $recommendation = createRecommendationForDetailsPage($user);

    ResearchSupport::query()->create([
        'user_id' => $user->id,
        'recommendation_id' => $recommendation->id,
        'suggested_title' => 'Minimal Draft',
        'abstract' => 'Abstract only draft.',
        'related_keywords' => [],
        'suggested_search_queries' => [],
        'suggested_journals' => [],
        'open_access_links' => [],
    ]);

    actingAs($user)
        ->get(route('recommendation.show', $recommendation))
        ->assertOk()
        ->assertSee('Google Scholar', false)
        ->assertSee('Needs manual verification', false)
        ->assertSee('Search links only. No automatic paper downloads.', false);
});
