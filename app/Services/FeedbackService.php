<?php

namespace App\Services;

use App\Models\Feedback;
use App\Models\Recommendation;

class FeedbackService
{
    public function storeFeedback(array $validatedData): Feedback
    {
        $recommendationId = $validatedData['recommendation_id'] ?? null;

        if ($recommendationId === '' || $recommendationId === 0 || $recommendationId === '0') {
            $recommendationId = null;
        }

        if ($recommendationId !== null && ! Recommendation::query()->whereKey($recommendationId)->exists()) {
            $recommendationId = null;
        }

        return Feedback::create([
            'recommendation_id' => $recommendationId,
            'rating' => $validatedData['rating'],
            'comment' => $validatedData['comment'] ?? null,
        ]);
    }
}
