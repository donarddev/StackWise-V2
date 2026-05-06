<?php

namespace App\Services;

use App\Models\Feedback;
use App\Models\Recommendation;

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
        $totalRecommendations = Recommendation::query()->count();
        $totalFeedback = Feedback::query()->count();

        return [
            [
                'label' => 'Total Recommendations',
                'value' => $totalRecommendations,
                'helper' => 'Saved recommendation records',
            ],
            [
                'label' => 'Average Confidence',
                'value' => $totalRecommendations > 0
                    ? round((float) Recommendation::query()->avg('confidence_score')) . '%'
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
                    ? number_format((float) Feedback::query()->avg('rating'), 1) . '/5'
                    : '0/5',
                'helper' => 'Average user satisfaction rating',
            ],
        ];
    }

    private function getMostRecommendedValue(string $column): string
    {
        $value = Recommendation::query()
            ->select($column)
            ->selectRaw('COUNT(*) as total')
            ->groupBy($column)
            ->orderByDesc('total')
            ->value($column);

        return $value ?: 'N/A';
    }

    private function getRecentRecommendations(): array
    {
        return Recommendation::query()
            ->latest()
            ->limit(5)
            ->get()
            ->all();
    }
}
