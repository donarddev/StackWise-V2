<?php

namespace App\Services;

use App\Models\Recommendation;
use App\Services\Recommendation\ProjectContext;
use App\Services\Recommendation\RecommendationEngine;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class RecommendationService
{
    public function __construct(
        private readonly RecommendationEngine $engine,
    ) {}

    public function generateRecommendation(array $validatedData): array
    {
        $context = ProjectContext::fromValidated($validatedData);

        return $this->engine->recommend($context);
    }

    public function generateAndStoreRecommendation(array $validatedData): Recommendation
    {
        $report = $this->generateRecommendation($validatedData);

        return $this->storeRecommendation($validatedData, $report);
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
            'project_types' => $this->scopeToCurrentUser(Recommendation::query())
                ->whereNotNull('project_type')
                ->distinct()
                ->orderBy('project_type')
                ->pluck('project_type')
                ->all(),
            'languages' => $this->scopeToCurrentUser(Recommendation::query())
                ->whereNotNull('recommended_language')
                ->distinct()
                ->orderBy('recommended_language')
                ->pluck('recommended_language')
                ->all(),
            'frameworks' => $this->scopeToCurrentUser(Recommendation::query())
                ->whereNotNull('recommended_framework')
                ->distinct()
                ->orderBy('recommended_framework')
                ->pluck('recommended_framework')
                ->all(),
            'sdlc_models' => $this->scopeToCurrentUser(Recommendation::query())
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
        $query = $this->scopeToCurrentUser(Recommendation::query());

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

    public function getRecommendationDetails(Recommendation $recommendation): array
    {
        $recommendation->loadMissing('feedback');

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
                'selected_features' => $recommendation->selected_features ?? [],
                'team_size' => $recommendation->team_size,
                'complexity' => $recommendation->complexity,
                'preferred_platform' => $recommendation->preferred_platform,
                'development_experience' => $recommendation->development_experience,
                'timeline' => $recommendation->timeline,
                'project_goal' => $recommendation->project_goal,
                'scalability_needs' => $recommendation->scalability_needs,
                'security_requirements' => $recommendation->security_requirements,
                'performance_requirements' => $recommendation->performance_requirements,
                'budget_constraints' => $recommendation->budget_constraints,
                'maintenance_expectations' => $recommendation->maintenance_expectations,
                'deployment_preference' => $recommendation->deployment_preference,
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
            'user_id' => auth()->id(),
            'project_name' => $validatedData['project_name'],
            'project_type' => $validatedData['project_type'],
            'selected_features' => $validatedData['selected_features'] ?? [],
            'team_size' => $validatedData['team_size'],
            'complexity' => $validatedData['complexity'],
            'preferred_platform' => $validatedData['preferred_platform'],
            'development_experience' => $validatedData['development_experience'],
            'timeline' => $validatedData['timeline'],
            'project_goal' => $validatedData['project_goal'],
            'scalability_needs' => $validatedData['scalability_needs'] ?? null,
            'security_requirements' => $validatedData['security_requirements'] ?? null,
            'performance_requirements' => $validatedData['performance_requirements'] ?? null,
            'budget_constraints' => $validatedData['budget_constraints'] ?? null,
            'maintenance_expectations' => $validatedData['maintenance_expectations'] ?? null,
            'deployment_preference' => $validatedData['deployment_preference'] ?? null,
            'requirements_stability' => $validatedData['requirements_stability'] ?? null,
            'stakeholder_involvement' => $validatedData['stakeholder_involvement'] ?? null,
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

    private function scopeToCurrentUser(Builder $query): Builder
    {
        $userId = auth()->id();
        if ($userId === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('user_id', $userId);
    }

    // Note: generation logic moved to dedicated engine classes under App\Services\Recommendation\
}
