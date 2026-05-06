<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackRequest;
use App\Services\FeedbackService;
use Illuminate\Http\RedirectResponse;

class FeedbackController extends Controller
{
    public function __construct(
        private readonly FeedbackService $feedbackService,
    ) {
    }

    public function store(FeedbackRequest $request): RedirectResponse
    {
        $this->feedbackService->storeFeedback($request->validated());

        return back()->with('feedback_success', 'Thank you for your feedback.');
    }
}
