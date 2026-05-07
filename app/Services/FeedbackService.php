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

        if ($recommendationId !== null) {
            $recommendation = Recommendation::query()->find($recommendationId);
            if (! $recommendation) {
                $recommendationId = null;
            } elseif ($recommendation->user_id !== null && $recommendation->user_id !== auth()->id()) {
                $recommendationId = null;
            }
        }

        return Feedback::create([
            'recommendation_id' => $recommendationId,
            'rating' => $validatedData['rating'],
            'comment' => $validatedData['comment'] ?? null,
        ]);
    }
}
