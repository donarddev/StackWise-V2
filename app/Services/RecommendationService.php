<?php

namespace App\Services;

use App\Models\Recommendation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class RecommendationService
{
    public function generateRecommendation(array $validatedData): array
    {
        $projectText = $this->buildProjectText($validatedData);
        $projectType = strtolower((string) ($validatedData['project_type'] ?? ''));
        $projectGoal = strtolower((string) ($validatedData['project_goal'] ?? ''));
        $analysisText = $projectText.' '.$projectGoal;

        $profile = $this->detectProfile($analysisText);
        $sdlc = $this->recommendSdlc($validatedData, $analysisText);
        $confidenceScore = $this->calculateConfidenceScore($profile, $validatedData, $sdlc, $analysisText);
        $userLevel = $this->mapUserLevel((string) $validatedData['development_experience']);

        $projectSummary = [
            'project_name' => $validatedData['project_name'],
            'project_type' => $validatedData['project_type'],
            'team_size' => $validatedData['team_size'],
            'complexity' => $validatedData['complexity'],
            'preferred_platform' => $validatedData['preferred_platform'],
            'development_experience' => $validatedData['development_experience'],
            'timeline' => $validatedData['timeline'],
            'project_goal' => $validatedData['project_goal'],
        ];

        $mainRecommendation = [
            'language' => $profile['language'],
            'framework' => $profile['framework'],
            'sdlc_model' => $sdlc['model'],
            'confidence_score' => $confidenceScore,
        ];

        $explanation = [
            'language_reason' => $profile['language_reason'],
            'framework_reason' => $profile['framework_reason'],
            'sdlc_reason' => $sdlc['reason'],
        ];

        $alternativeStacks = $this->buildAlternativeStacks($profile, $validatedData, $sdlc['model']);
        $whyNotThis = $this->buildWhyNotThis($profile, $sdlc['model']);
        $riskAnalysis = $this->buildRiskAnalysis($validatedData, $analysisText, $profile, $sdlc['model']);
        $skillGapAnalysis = $this->buildSkillGapAnalysis($profile, $userLevel);
        $projectRoadmap = $this->buildProjectRoadmap($profile['type']);

        // Future database logic can persist recommendations for history and analytics.
        // Future FastAPI integration can replace these rules with AI-assisted scoring.
        // Future Ollama chatbot integration can explain these results in natural language.

        return [
            'project_summary' => $projectSummary,
            'main_recommendation' => $mainRecommendation,
            'explanation' => $explanation,
            'alternative_stacks' => $alternativeStacks,
            'why_not_this' => $whyNotThis,
            'risk_analysis' => $riskAnalysis,
            'skill_gap_analysis' => $skillGapAnalysis,
            'project_roadmap' => $projectRoadmap,
            'feedback' => [],
        ];
    }

    public function generateAndStoreRecommendation(array $validatedData): array
    {
        $report = $this->generateRecommendation($validatedData);
        $record = $this->storeRecommendation($validatedData, $report);

        return [
            'report' => $report,
            'record' => $record,
        ];
    }

    public function getHistory(int $perPage = 10): LengthAwarePaginator
    {
        return $this->getHistoryPagePayload([], $perPage)['recommendations'];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{
     *     recommendations: LengthAwarePaginator,
     *     summary: array{
     *         saved_records: int,
     *         average_confidence: float|null,
     *         most_recommended_language: string|null,
     *         most_recommended_framework: string|null,
     *         current_page: int
     *     },
     *     insights: array{
     *         most_common_stack: string|null,
     *         highest_confidence: array{project: string, score: int}|null,
     *         latest: array{project: string, date: Carbon}|null
     *     }|null,
     *     filterOptions: array{
     *         project_types: list<string>,
     *         languages: list<string>,
     *         frameworks: list<string>,
     *         sdlc_models: list<string>
     *     },
     *     filters: array<string, mixed>,
     *     hasActiveFilters: bool
     * }
     */
    public function getHistoryPagePayload(array $filters, int $perPage = 10): array
    {
        $filters = $this->normalizeHistoryFilters($filters);
        $query = $this->historyFilteredQuery($filters);
        $filterOptions = $this->getHistoryFilterOptions();

        $total = (clone $query)->count();
        $summary = $this->buildHistorySummaryStats($query, $total);
        $insights = $total > 0 ? $this->buildHistoryInsights($query) : null;

        $paginator = (clone $query)->paginate($perPage)->withQueryString();
        $summary['saved_records'] = $paginator->total();
        $summary['current_page'] = $paginator->currentPage();

        return [
            'recommendations' => $paginator,
            'summary' => $summary,
            'insights' => $insights,
            'filterOptions' => $filterOptions,
            'filters' => $filters,
            'hasActiveFilters' => $this->historyHasActiveFilters($filters),
        ];
    }

    /**
     * @return array{
     *     project_types: list<string>,
     *     languages: list<string>,
     *     frameworks: list<string>,
     *     sdlc_models: list<string>
     * }
     */
    public function getHistoryFilterOptions(): array
    {
        return [
            'project_types' => Recommendation::query()
                ->whereNotNull('project_type')
                ->distinct()
                ->orderBy('project_type')
                ->pluck('project_type')
                ->all(),
            'languages' => Recommendation::query()
                ->whereNotNull('recommended_language')
                ->distinct()
                ->orderBy('recommended_language')
                ->pluck('recommended_language')
                ->all(),
            'frameworks' => Recommendation::query()
                ->whereNotNull('recommended_framework')
                ->distinct()
                ->orderBy('recommended_framework')
                ->pluck('recommended_framework')
                ->all(),
            'sdlc_models' => Recommendation::query()
                ->whereNotNull('recommended_sdlc_model')
                ->distinct()
                ->orderBy('recommended_sdlc_model')
                ->pluck('recommended_sdlc_model')
                ->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    private function normalizeHistoryFilters(array $filters): array
    {
        return [
            'search' => trim((string) ($filters['search'] ?? '')),
            'project_type' => $this->nullableString($filters['project_type'] ?? null),
            'language' => $this->nullableString($filters['language'] ?? null),
            'framework' => $this->nullableString($filters['framework'] ?? null),
            'sdlc_model' => $this->nullableString($filters['sdlc_model'] ?? null),
            'sort' => $this->nullableString($filters['sort'] ?? null) ?? 'latest',
            'confidence_min' => array_key_exists('confidence_min', $filters) && $filters['confidence_min'] !== null && $filters['confidence_min'] !== ''
                ? (int) $filters['confidence_min']
                : null,
            'confidence_max' => array_key_exists('confidence_max', $filters) && $filters['confidence_max'] !== null && $filters['confidence_max'] !== ''
                ? (int) $filters['confidence_max']
                : null,
        ];
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (string) $value;
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function historyHasActiveFilters(array $filters): bool
    {
        if ($filters['search'] !== '') {
            return true;
        }

        foreach (['project_type', 'language', 'framework', 'sdlc_model'] as $key) {
            if ($filters[$key] !== null) {
                return true;
            }
        }

        if ($filters['confidence_min'] !== null || $filters['confidence_max'] !== null) {
            return true;
        }

        if (($filters['sort'] ?? 'latest') !== 'latest') {
            return true;
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function historyFilteredQuery(array $filters): Builder
    {
        $query = Recommendation::query();

        if ($filters['search'] !== '') {
            $like = '%'.$this->escapeLike($filters['search']).'%';
            $query->where(function (Builder $q) use ($like): void {
                $q->where('project_name', 'like', $like)
                    ->orWhere('project_type', 'like', $like)
                    ->orWhere('recommended_language', 'like', $like)
                    ->orWhere('recommended_framework', 'like', $like)
                    ->orWhere('recommended_sdlc_model', 'like', $like);
            });
        }

        if ($filters['project_type'] !== null) {
            $query->where('project_type', $filters['project_type']);
        }

        if ($filters['language'] !== null) {
            $query->where('recommended_language', $filters['language']);
        }

        if ($filters['framework'] !== null) {
            $query->where('recommended_framework', $filters['framework']);
        }

        if ($filters['sdlc_model'] !== null) {
            $query->where('recommended_sdlc_model', $filters['sdlc_model']);
        }

        if ($filters['confidence_min'] !== null) {
            $query->where('confidence_score', '>=', $filters['confidence_min']);
        }

        if ($filters['confidence_max'] !== null) {
            $query->where('confidence_score', '<=', $filters['confidence_max']);
        }

        $this->applyHistorySort($query, $filters['sort'] ?? 'latest');

        return $query;
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }

    private function applyHistorySort(Builder $query, string $sort): void
    {
        match ($sort) {
            'oldest' => $query->orderBy('created_at')->orderBy('id'),
            'confidence_desc' => $query->orderByDesc('confidence_score')->orderByDesc('id'),
            'confidence_asc' => $query->orderBy('confidence_score')->orderBy('id'),
            default => $query->orderByDesc('created_at')->orderByDesc('id'),
        };
    }

    /**
     * @return array{
     *     saved_records: int,
     *     average_confidence: float|null,
     *     most_recommended_language: string|null,
     *     most_recommended_framework: string|null,
     *     current_page: int
     * }
     */
    private function buildHistorySummaryStats(Builder $baseQuery, int $total): array
    {
        if ($total === 0) {
            return [
                'saved_records' => 0,
                'average_confidence' => null,
                'most_recommended_language' => null,
                'most_recommended_framework' => null,
                'current_page' => 1,
            ];
        }

        $avg = (clone $baseQuery)->reorder()->avg('confidence_score');

        $topLanguage = (clone $baseQuery)
            ->reorder()
            ->selectRaw('recommended_language, COUNT(*) as aggregate')
            ->groupBy('recommended_language')
            ->orderByDesc('aggregate')
            ->orderBy('recommended_language')
            ->first();

        $topFramework = (clone $baseQuery)
            ->reorder()
            ->selectRaw('recommended_framework, COUNT(*) as aggregate')
            ->groupBy('recommended_framework')
            ->orderByDesc('aggregate')
            ->orderBy('recommended_framework')
            ->first();

        return [
            'saved_records' => $total,
            'average_confidence' => $avg !== null ? round((float) $avg, 1) : null,
            'most_recommended_language' => $topLanguage?->recommended_language,
            'most_recommended_framework' => $topFramework?->recommended_framework,
            'current_page' => 1,
        ];
    }

    /**
     * @return array{
     *     most_common_stack: string|null,
     *     highest_confidence: array{project: string, score: int}|null,
     *     latest: array{project: string, date: Carbon}|null
     * }|null
     */
    private function buildHistoryInsights(Builder $baseQuery): ?array
    {
        $stackRow = (clone $baseQuery)
            ->reorder()
            ->selectRaw('recommended_language, recommended_framework, recommended_sdlc_model, COUNT(*) as aggregate')
            ->groupBy('recommended_language', 'recommended_framework', 'recommended_sdlc_model')
            ->orderByDesc('aggregate')
            ->orderBy('recommended_language')
            ->first();

        $highest = (clone $baseQuery)->reorder()->orderByDesc('confidence_score')->orderByDesc('id')->first();
        $latest = (clone $baseQuery)->reorder()->orderByDesc('created_at')->orderByDesc('id')->first();

        return [
            'most_common_stack' => $stackRow
                ? $stackRow->recommended_language.' + '.$stackRow->recommended_framework.' · '.$stackRow->recommended_sdlc_model
                : null,
            'highest_confidence' => $highest
                ? ['project' => $highest->project_name, 'score' => (int) $highest->confidence_score]
                : null,
            'latest' => $latest && $latest->created_at
                ? ['project' => $latest->project_name, 'date' => $latest->created_at]
                : null,
        ];
    }

    public function getRecommendationDetails(int $id): array
    {
        $recommendation = Recommendation::query()
            ->with('feedback')
            ->findOrFail($id);

        return [
            'recommendation' => $recommendation,
            'recommendationRecord' => $recommendation,
            'recommendationReport' => $this->buildRecommendationReportFromModel($recommendation),
        ];
    }

    public function buildRecommendationReportFromModel(Recommendation $recommendation): array
    {
        return [
            'project_summary' => [
                'project_name' => $recommendation->project_name,
                'project_type' => $recommendation->project_type,
                'team_size' => $recommendation->team_size,
                'complexity' => $recommendation->complexity,
                'preferred_platform' => $recommendation->preferred_platform,
                'development_experience' => $recommendation->development_experience,
                'timeline' => $recommendation->timeline,
                'project_goal' => $recommendation->project_goal,
            ],
            'main_recommendation' => [
                'language' => $recommendation->recommended_language,
                'framework' => $recommendation->recommended_framework,
                'sdlc_model' => $recommendation->recommended_sdlc_model,
                'confidence_score' => $recommendation->confidence_score,
            ],
            'explanation' => [
                'language_reason' => $recommendation->explanations['language_reason'] ?? '',
                'framework_reason' => $recommendation->explanations['framework_reason'] ?? '',
                'sdlc_reason' => $recommendation->explanations['sdlc_reason'] ?? '',
            ],
            'alternative_stacks' => $recommendation->alternative_stacks ?? [],
            'why_not_this' => $recommendation->explanations['why_not_this'] ?? [],
            'risk_analysis' => $recommendation->risk_analysis ?? [],
            'skill_gap_analysis' => $recommendation->skill_gap_analysis ?? [],
            'project_roadmap' => $recommendation->roadmap ?? [],
            'feedback' => $recommendation->feedback->map(static function ($feedback): array {
                return [
                    'rating' => $feedback->rating,
                    'comment' => $feedback->comment,
                    'created_at' => $feedback->created_at,
                ];
            })->all(),
        ];
    }

    private function buildProjectText(array $validatedData): string
    {
        return strtolower(implode(' ', [
            $validatedData['project_name'] ?? '',
            $validatedData['project_type'] ?? '',
            $validatedData['project_goal'] ?? '',
        ]));
    }

    private function storeRecommendation(array $validatedData, array $report): Recommendation
    {
        return Recommendation::create([
            'project_name' => $validatedData['project_name'],
            'project_type' => $validatedData['project_type'],
            'team_size' => $validatedData['team_size'],
            'complexity' => $validatedData['complexity'],
            'preferred_platform' => $validatedData['preferred_platform'],
            'development_experience' => $validatedData['development_experience'],
            'timeline' => $validatedData['timeline'],
            'project_goal' => $validatedData['project_goal'],
            'recommended_language' => $report['main_recommendation']['language'],
            'recommended_framework' => $report['main_recommendation']['framework'],
            'recommended_sdlc_model' => $report['main_recommendation']['sdlc_model'],
            'confidence_score' => $report['main_recommendation']['confidence_score'],
            'explanations' => [
                'language_reason' => $report['explanation']['language_reason'],
                'framework_reason' => $report['explanation']['framework_reason'],
                'sdlc_reason' => $report['explanation']['sdlc_reason'],
                'why_not_this' => $report['why_not_this'],
            ],
            'alternative_stacks' => $report['alternative_stacks'],
            'risk_analysis' => $report['risk_analysis'],
            'skill_gap_analysis' => $report['skill_gap_analysis'],
            'roadmap' => $report['project_roadmap'],
        ]);
    }

    private function detectProfile(string $analysisText): array
    {
        if ($this->containsAny($analysisText, ['ai', 'chatbot', 'machine learning', 'machine-learning', 'data'])) {
            return [
                'type' => 'ai',
                'language' => 'Python',
                'framework' => 'FastAPI',
                'language_reason' => 'The project mentions AI, chatbot, machine learning, or data, so Python is a strong fit for AI workflows and scripting.',
                'framework_reason' => 'FastAPI is lightweight and works well for API-driven AI services and model integration.',
                'alternatives' => [
                    ['language' => 'Python', 'framework' => 'Django', 'best_for' => 'Full-featured AI web applications', 'score' => 88, 'limitation' => 'Heavier than FastAPI for a small API-only project'],
                    ['language' => 'Python', 'framework' => 'Flask', 'best_for' => 'Very small services and prototypes', 'score' => 82, 'limitation' => 'Needs more manual setup than FastAPI'],
                ],
                'why_not' => [
                    'Java was not selected because it may require more setup time for beginner teams.',
                    'Pure Laravel was not selected for AI-heavy projects because Python has stronger AI support.',
                ],
            ];
        }

        if ($this->containsAny($analysisText, ['mobile', 'android', 'ios'])) {
            return [
                'type' => 'mobile',
                'language' => 'Dart',
                'framework' => 'Flutter',
                'language_reason' => 'The project mentions mobile, Android, or iOS, so Dart gives one codebase for multiple mobile platforms.',
                'framework_reason' => 'Flutter is beginner-friendly for mobile UI development and keeps the app structure consistent.',
                'alternatives' => [
                    ['language' => 'Kotlin', 'framework' => 'Android SDK', 'best_for' => 'Android-only applications', 'score' => 84, 'limitation' => 'Does not cover iOS with the same codebase'],
                    ['language' => 'Swift', 'framework' => 'SwiftUI', 'best_for' => 'iOS-only applications', 'score' => 82, 'limitation' => 'Does not cover Android with the same codebase'],
                ],
                'why_not' => [
                    'PHP was not selected because it is not the primary choice for native mobile apps.',
                    'Node.js was not selected because it is better suited for server-side or real-time services than mobile UI apps.',
                ],
            ];
        }

        if ($this->containsAny($analysisText, ['real-time', 'realtime', 'chat', 'messaging', 'live update', 'live-updates'])) {
            return [
                'type' => 'realtime',
                'language' => 'TypeScript',
                'framework' => 'Node.js',
                'language_reason' => 'The project mentions real-time chat, messaging, or live updates, so TypeScript is a practical choice for maintainable interactive apps.',
                'framework_reason' => 'Node.js is event-driven and fits real-time communication features very well.',
                'alternatives' => [
                    ['language' => 'JavaScript', 'framework' => 'Node.js', 'best_for' => 'Fast prototyping with real-time features', 'score' => 86, 'limitation' => 'Less type safety than TypeScript'],
                    ['language' => 'TypeScript', 'framework' => 'Express', 'best_for' => 'Custom real-time backends', 'score' => 83, 'limitation' => 'Requires more manual architecture decisions'],
                ],
                'why_not' => [
                    'Waterfall was not selected because real-time features often need quick iteration and feedback.',
                    'Pure Laravel was not selected because Node.js is usually a better fit for event-driven live communication.',
                ],
            ];
        }

        if ($this->containsAny($analysisText, ['web', 'crud', 'inventory', 'management', 'e-commerce', 'ecommerce'])) {
            return [
                'type' => 'web',
                'language' => 'PHP',
                'framework' => 'Laravel',
                'language_reason' => 'The project mentions web, CRUD, inventory, management, or e-commerce, so PHP is a strong and familiar web choice.',
                'framework_reason' => 'Laravel is excellent for structured web development, routing, and validation in a student project.',
                'alternatives' => [
                    ['language' => 'JavaScript', 'framework' => 'Express.js', 'best_for' => 'Custom web applications with JavaScript on both sides', 'score' => 84, 'limitation' => 'Requires more manual setup than Laravel'],
                    ['language' => 'Python', 'framework' => 'Django', 'best_for' => 'Web apps that may also grow into data-driven features', 'score' => 82, 'limitation' => 'Heavier learning curve for complete beginners'],
                ],
                'why_not' => [
                    'Java was not selected because it may require more setup time for beginner teams.',
                    'Pure Laravel was selected here because it is already the best fit for the given web-focused requirements.',
                ],
            ];
        }

        return [
            'type' => 'general',
            'language' => 'PHP',
            'framework' => 'Laravel',
            'language_reason' => 'The project does not strongly match a specialized stack, so PHP offers a beginner-friendly and flexible default choice.',
            'framework_reason' => 'Laravel gives a clean MVC structure and is easy to present in a student project.',
            'alternatives' => [
                ['language' => 'Python', 'framework' => 'FastAPI', 'best_for' => 'API-first projects and AI-friendly backends', 'score' => 80, 'limitation' => 'Less direct if the project is mostly standard web CRUD'],
                ['language' => 'TypeScript', 'framework' => 'Node.js', 'best_for' => 'Interactive apps and real-time backends', 'score' => 79, 'limitation' => 'More tooling setup than Laravel for a beginner team'],
            ],
            'why_not' => [
                'Java was not selected because it may require more setup time for beginner teams.',
                'Waterfall was not selected because the project description suggests a student project that may benefit from iterative delivery.',
            ],
        ];
    }

    private function recommendSdlc(array $validatedData, string $analysisText): array
    {
        $complexity = strtolower((string) ($validatedData['complexity'] ?? ''));
        $timeline = strtolower((string) ($validatedData['timeline'] ?? ''));
        $requirementsChanging = $this->containsAny($analysisText, ['changing', 'flexible', 'evolving', 'dynamic', 'uncertain']);

        if ($timeline === 'short' || $requirementsChanging) {
            return [
                'model' => 'Agile',
                'reason' => 'A short timeline or changing requirements are better managed with iterative planning, small increments, and regular feedback.',
            ];
        }

        if ($complexity === 'high' || $this->containsAny($analysisText, ['high risk', 'critical', 'sensitive', 'complex'])) {
            return [
                'model' => 'Spiral',
                'reason' => 'High-risk or highly complex work benefits from repeated evaluation, prototyping, and risk review.',
            ];
        }

        if (in_array($complexity, ['low', 'medium'], true) && ! $requirementsChanging) {
            return [
                'model' => 'Waterfall',
                'reason' => 'Stable requirements with low to medium complexity can be planned in a simple sequential flow.',
            ];
        }

        return [
            'model' => 'Iterative',
            'reason' => 'Iterative development is a balanced default when the project needs a few controlled revisions before final delivery.',
        ];
    }

    private function calculateConfidenceScore(array $profile, array $validatedData, array $sdlc, string $analysisText): int
    {
        $confidenceScore = 70;

        if ($profile['type'] !== 'general') {
            $confidenceScore += 12;
        }

        if ($sdlc['model'] === 'Agile') {
            $confidenceScore += 4;
        }

        if ($this->containsAny($analysisText, ['ai', 'mobile', 'web', 'chat', 'data'])) {
            $confidenceScore += 6;
        }

        if (strtolower((string) ($validatedData['complexity'] ?? '')) === 'high') {
            $confidenceScore -= 5;
        }

        if (strtolower((string) ($validatedData['timeline'] ?? '')) === 'short') {
            $confidenceScore -= 4;
        }

        return max(55, min(95, $confidenceScore));
    }

    private function buildAlternativeStacks(array $profile, array $validatedData, string $sdlcModel): array
    {
        $alternatives = $profile['alternatives'];

        $span = strtolower((string) ($validatedData['project_goal'] ?? ''));
        if ($this->containsAny($span, ['budget', 'simple', 'basic'])) {
            $alternatives[1]['limitation'] .= ' It may still feel more complex than needed for a simple project.';
        }

        return array_map(static function (array $alternative) use ($sdlcModel): array {
            $alternative['best_for'] .= ' Recommended with '.$sdlcModel.' for phased delivery.';

            return $alternative;
        }, $alternatives);
    }

    private function buildWhyNotThis(array $profile, string $sdlcModel): array
    {
        $reasons = $profile['why_not'];

        $reasons[] = $sdlcModel === 'Waterfall'
            ? 'Agile was not selected because the requirements appear stable enough for a more direct plan.'
            : 'Waterfall was not selected because changing requirements are better handled by an iterative model.';

        return array_values(array_unique($reasons));
    }

    private function buildRiskAnalysis(array $validatedData, string $analysisText, array $profile, string $sdlcModel): array
    {
        $risks = [
            [
                'risk_title' => 'Changing Requirements',
                'impact_level' => 'Medium',
                'explanation' => 'If the project scope changes often, the team may need to revisit features and design decisions.',
                'suggested_solution' => 'Use short planning cycles, keep a backlog, and review requirements regularly.',
            ],
        ];

        if ($this->containsAny($analysisText, ['ai', 'chatbot', 'machine learning', 'data'])) {
            $risks[] = [
                'risk_title' => 'Local AI Setup',
                'impact_level' => 'Medium',
                'explanation' => 'Ollama or other local AI tools may require correct installation and enough hardware resources.',
                'suggested_solution' => 'Test the environment early and keep a fallback plan for demo day.',
            ];
        }

        if ($this->containsAny($analysisText, ['mobile', 'android', 'ios'])) {
            $risks[] = [
                'risk_title' => 'Platform Testing',
                'impact_level' => 'Medium',
                'explanation' => 'Mobile projects need testing on more than one device size and platform behavior.',
                'suggested_solution' => 'Prepare a small test matrix and verify core screens on emulators or real devices.',
            ];
        }

        if ($profile['type'] === 'realtime') {
            $risks[] = [
                'risk_title' => 'Connection Stability',
                'impact_level' => 'Medium',
                'explanation' => 'Real-time features depend on stable sockets or live updates, which can be harder to debug.',
                'suggested_solution' => 'Build the real-time layer in small steps and test message flow early.',
            ];
        }

        if ($validatedData['team_size'] > 4) {
            $risks[] = [
                'risk_title' => 'Coordination Overhead',
                'impact_level' => 'Low',
                'explanation' => 'A larger team may need clearer task ownership to avoid duplicated work.',
                'suggested_solution' => 'Assign modules early and track responsibilities in a shared task list.',
            ];
        }

        if ($sdlcModel === 'Waterfall') {
            $risks[] = [
                'risk_title' => 'Late Change Cost',
                'impact_level' => 'Medium',
                'explanation' => 'In Waterfall, changes can be more expensive if they appear after the design phase.',
                'suggested_solution' => 'Confirm requirements carefully before coding begins.',
            ];
        }

        return array_slice($risks, 0, 3);
    }

    private function buildSkillGapAnalysis(array $profile, string $userLevel): array
    {
        $skills = match ($profile['type']) {
            'ai' => [
                ['skill' => 'Python fundamentals', 'required_level' => 'Intermediate', 'suggestion' => 'Practice syntax and file handling before adding AI features.'],
                ['skill' => 'FastAPI basics', 'required_level' => 'Intermediate', 'suggestion' => 'Build one small API endpoint and connect it to the front end.'],
                ['skill' => 'AI integration', 'required_level' => 'Intermediate', 'suggestion' => 'Use a small, testable chatbot or model demo first.'],
            ],
            'mobile' => [
                ['skill' => 'Dart fundamentals', 'required_level' => 'Beginner', 'suggestion' => 'Learn variables, widgets, and simple navigation first.'],
                ['skill' => 'Flutter UI layout', 'required_level' => 'Intermediate', 'suggestion' => 'Practice building responsive mobile screens with reusable widgets.'],
                ['skill' => 'State management', 'required_level' => 'Intermediate', 'suggestion' => 'Keep the first version simple with basic state handling.'],
            ],
            'realtime' => [
                ['skill' => 'TypeScript fundamentals', 'required_level' => 'Intermediate', 'suggestion' => 'Use typed objects and interfaces to avoid bugs.'],
                ['skill' => 'Node.js event handling', 'required_level' => 'Intermediate', 'suggestion' => 'Start with one socket event and one response event.'],
                ['skill' => 'API integration', 'required_level' => 'Intermediate', 'suggestion' => 'Connect the front end and back end in small steps.'],
            ],
            default => [
                ['skill' => 'Laravel routing', 'required_level' => 'Beginner', 'suggestion' => 'Build one page at a time and keep routes organized.'],
                ['skill' => 'Blade templating', 'required_level' => 'Beginner', 'suggestion' => 'Use reusable layouts and partials for consistency.'],
                ['skill' => 'Form validation', 'required_level' => 'Beginner', 'suggestion' => 'Keep validation inside Form Request classes.'],
            ],
        };

        return array_map(function (array $skill) use ($userLevel): array {
            $skill['user_level'] = $userLevel;
            $skill['gap_level'] = $this->compareLevels($userLevel, $skill['required_level']);

            return $skill;
        }, $skills);
    }

    private function buildProjectRoadmap(string $profileType): array
    {
        $roadmap = [
            ['phase' => 'Phase 1', 'task' => 'Requirements and Planning', 'description' => 'Define the problem, scope, and success criteria clearly.'],
            ['phase' => 'Phase 2', 'task' => 'UI Design and Laravel Layout', 'description' => 'Create the shared layout, navigation, and reusable Blade sections.'],
            ['phase' => 'Phase 3', 'task' => 'Recommendation Engine Development', 'description' => 'Build the rule-based recommendation service and connect it to the form.'],
            ['phase' => 'Phase 4', 'task' => 'Documentation Explorer', 'description' => 'Prepare guides, references, and module content for future expansion.'],
            ['phase' => 'Phase 5', 'task' => 'Chatbot Interface', 'description' => 'Add the chatbot page and connect it to the AI integration later.'],
            ['phase' => 'Phase 6', 'task' => 'Testing and Finalization', 'description' => 'Review the outputs, test the flow, and prepare the final presentation.'],
        ];

        if ($profileType === 'ai') {
            $roadmap[2]['description'] = 'Build the Python and FastAPI integration first, then connect the front end to the AI service.';
        }

        return $roadmap;
    }

    private function mapUserLevel(string $developmentExperience): string
    {
        return match (strtolower($developmentExperience)) {
            'advanced' => 'Advanced',
            'intermediate' => 'Intermediate',
            default => 'Beginner',
        };
    }

    private function compareLevels(string $userLevel, string $requiredLevel): string
    {
        $levels = [
            'Beginner' => 1,
            'Intermediate' => 2,
            'Advanced' => 3,
        ];

        $difference = ($levels[$requiredLevel] ?? 1) - ($levels[$userLevel] ?? 1);

        return match (true) {
            $difference <= 0 => 'No gap',
            $difference === 1 => 'Small gap',
            default => 'Medium gap',
        };
    }

    private function containsAny(string $text, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($text, strtolower($keyword))) {
                return true;
            }
        }

        return false;
    }
}
