<?php

namespace App\Services;

use App\Models\Feedback;
use App\Models\Recommendation;
use Illuminate\Database\Eloquent\Builder;

class DashboardService
{
    public function getDashboardData(): array
    {
        return [
            'statistics' => $this->getStatistics(),
            'recentRecommendations' => $this->getRecentRecommendations(),
        ];
    }

    private function getStatistics(): array
    {
        $recommendationsQuery = $this->scopeToCurrentUser(Recommendation::query());
        $totalRecommendations = (clone $recommendationsQuery)->count();

        $totalFeedback = Feedback::query()
            ->when(auth()->id() !== null, function (Builder $query): void {
                $query->whereHas('recommendation', function (Builder $recommendations): void {
                    $this->scopeToCurrentUser($recommendations);
                });
            })
            ->count();

        return [
            [
                'label' => 'Total Recommendations',
                'value' => $totalRecommendations,
                'helper' => 'Saved recommendation records',
            ],
            [
                'label' => 'Average Confidence',
                'value' => $totalRecommendations > 0
                    ? round((float) (clone $recommendationsQuery)->avg('confidence_score')).'%'
                    : '0%',
                'helper' => 'Average score from all recommendations',
            ],
            [
                'label' => 'Top Language',
                'value' => $this->getMostRecommendedValue('recommended_language'),
                'helper' => 'Most frequently suggested language',
            ],
            [
                'label' => 'Top Framework',
                'value' => $this->getMostRecommendedValue('recommended_framework'),
                'helper' => 'Most frequently suggested framework',
            ],
            [
                'label' => 'Top SDLC Model',
                'value' => $this->getMostRecommendedValue('recommended_sdlc_model'),
                'helper' => 'Most frequently suggested process model',
            ],
            [
                'label' => 'Total Feedback',
                'value' => $totalFeedback,
                'helper' => 'Feedback submissions received',
            ],
            [
                'label' => 'Average Rating',
                'value' => $totalFeedback > 0
                    ? number_format((float) Feedback::query()
                        ->when(auth()->id() !== null, function (Builder $query): void {
                            $query->whereHas('recommendation', function (Builder $recommendations): void {
                                $this->scopeToCurrentUser($recommendations);
                            });
                        })
                        ->avg('rating'), 1).'/5'
                    : '0/5',
                'helper' => 'Average user satisfaction rating',
            ],
        ];
    }

    private function getMostRecommendedValue(string $column): string
    {
        $value = $this->scopeToCurrentUser(Recommendation::query())
            ->select($column)
            ->selectRaw('COUNT(*) as total')
            ->groupBy($column)
            ->orderByDesc('total')
            ->value($column);

        return $value ?: 'N/A';
    }

    private function getRecentRecommendations(): array
    {
        return $this->scopeToCurrentUser(Recommendation::query())
            ->latest()
            ->limit(5)
            ->get()
            ->all();
    }

    private function scopeToCurrentUser(Builder $query): Builder
    {
        $userId = auth()->id();
        if ($userId === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('user_id', $userId);
    }
}
