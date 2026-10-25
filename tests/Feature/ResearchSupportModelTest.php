<?php

use App\Models\Recommendation;
use App\Models\ResearchSupport;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

function createRecommendationForUser(User $user): Recommendation
{
    return Recommendation::query()->create([
        'user_id' => $user->id,
        'project_name' => 'Test Project',
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
}

test('research supports table includes academic structure columns', function () {
    $jsonColumns = [
        'title_page',
        'objectives',
        'review_of_related_literature',
        'references',
        'research_discovery',
    ];

    $longTextColumns = [
        'conceptual_framework',
        'technology_stack_justification',
        'risk_analysis_summary',
        'skill_gap_summary',
        'roadmap_summary',
        'conclusion',
        'recommendations',
    ];

    foreach (array_merge($jsonColumns, $longTextColumns) as $column) {
        expect(Schema::hasColumn('research_supports', $column))->toBeTrue();
    }
});

test('research support stores academic structure json fields as arrays', function () {
    $user = User::factory()->create();
    $recommendation = createRecommendationForUser($user);

    $researchSupport = ResearchSupport::query()->create([
        'user_id' => $user->id,
        'recommendation_id' => $recommendation->id,
        'title_page' => ['title' => 'Capstone Study', 'program' => 'BSIT'],
        'objectives' => ['general' => 'Build the system', 'specific' => ['Implement modules']],
        'review_of_related_literature' => ['themes' => ['Agile adoption']],
        'references' => [['citation' => 'Author (2024). Title.']],
        'research_discovery' => ['journals' => ['Example Journal'], 'queries' => ['laravel capstone']],
    ]);

    $researchSupport->refresh();

    expect($researchSupport->title_page)->toBe(['title' => 'Capstone Study', 'program' => 'BSIT'])
        ->and($researchSupport->objectives)->toBe(['general' => 'Build the system', 'specific' => ['Implement modules']])
        ->and($researchSupport->review_of_related_literature)->toBe(['themes' => ['Agile adoption']])
        ->and($researchSupport->references)->toBe([['citation' => 'Author (2024). Title.']])
        ->and($researchSupport->research_discovery)->toBe(['journals' => ['Example Journal'], 'queries' => ['laravel capstone']]);
});

test('legacy statement of problem json is readable as an array', function () {
    $user = User::factory()->create();
    $recommendation = createRecommendationForUser($user);

    $researchSupport = ResearchSupport::query()->create([
        'user_id' => $user->id,
        'recommendation_id' => $recommendation->id,
    ]);

    DB::table('research_supports')
        ->where('id', $researchSupport->id)
        ->update([
            'statement_of_problem' => json_encode(['content' => 'Legacy problem statement text']),
        ]);

    $researchSupport->refresh();

    expect($researchSupport->statement_of_problem)->toBe([
        'content' => 'Legacy problem statement text',
    ]);
});

test('research support stores json fields as arrays', function () {
    $user = User::factory()->create();
    $recommendation = createRecommendationForUser($user);

    $researchSupport = ResearchSupport::query()->create([
        'user_id' => $user->id,
        'recommendation_id' => $recommendation->id,
        'specific_objectives' => ['Objective A', 'Objective B'],
        'related_keywords' => ['laravel', 'php'],
        'suggested_search_queries' => ['laravel research'],
        'suggested_journals' => ['Journal of Web Engineering'],
        'open_access_links' => ['https://example.com/paper'],
    ]);

    $researchSupport->refresh();

    expect($researchSupport->specific_objectives)->toBe(['Objective A', 'Objective B'])
        ->and($researchSupport->related_keywords)->toBe(['laravel', 'php'])
        ->and($researchSupport->suggested_search_queries)->toBe(['laravel research'])
        ->and($researchSupport->suggested_journals)->toBe(['Journal of Web Engineering'])
        ->and($researchSupport->open_access_links)->toBe(['https://example.com/paper']);
});

test('user has many research supports and recommendation has one research support', function () {
    $user = User::factory()->create();
    $firstRecommendation = createRecommendationForUser($user);
    $secondRecommendation = createRecommendationForUser($user);

    ResearchSupport::query()->create([
        'user_id' => $user->id,
        'recommendation_id' => $firstRecommendation->id,
    ]);

    ResearchSupport::query()->create([
        'user_id' => $user->id,
        'recommendation_id' => $secondRecommendation->id,
    ]);

    expect($user->researchSupports)->toHaveCount(2)
        ->and($firstRecommendation->researchSupport)->not->toBeNull()
        ->and($firstRecommendation->researchSupport->recommendation_id)->toBe($firstRecommendation->id);
});

test('only one research support record is allowed per recommendation', function () {
    $user = User::factory()->create();
    $recommendation = createRecommendationForUser($user);

    ResearchSupport::query()->create([
        'user_id' => $user->id,
        'recommendation_id' => $recommendation->id,
    ]);

    expect(fn () => ResearchSupport::query()->create([
        'user_id' => $user->id,
        'recommendation_id' => $recommendation->id,
    ]))->toThrow(QueryException::class);
});

test('research support is deleted when recommendation is deleted', function () {
    $user = User::factory()->create();
    $recommendation = createRecommendationForUser($user);

    $researchSupport = ResearchSupport::query()->create([
        'user_id' => $user->id,
        'recommendation_id' => $recommendation->id,
    ]);

    $recommendation->delete();

    expect(ResearchSupport::query()->find($researchSupport->id))->toBeNull();
});

test('research support is deleted when user is deleted', function () {
    $user = User::factory()->create();
    $recommendation = createRecommendationForUser($user);

    $researchSupport = ResearchSupport::query()->create([
        'user_id' => $user->id,
        'recommendation_id' => $recommendation->id,
    ]);

    $user->delete();

    expect(ResearchSupport::query()->find($researchSupport->id))->toBeNull();
});
