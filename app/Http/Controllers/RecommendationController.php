<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecommendationHistoryFilterRequest;
use App\Http\Requests\RecommendationRequest;
use App\Models\Recommendation;
use App\Services\RecommendationFormService;
use App\Services\RecommendationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class RecommendationController extends Controller
{
    public function __construct(
        private readonly RecommendationFormService $recommendationFormService,
        private readonly RecommendationService $recommendationService,
    ) {}

    public function index(): View
    {
        $formData = $this->recommendationFormService->getFormPageData();
        // Remove 'header' to prevent conflict with layout's $header variable
        $header = $formData['header'];
        unset($formData['header']);
        $formData['pageHeader'] = $header;

        return view('recommendation.create', $formData);
    }

    public function generate(RecommendationRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $recommendation = $this->recommendationService->generateAndStoreRecommendation($validated);

        return redirect()
            ->route('recommendation.show', $recommendation)
            ->with('success', 'Recommendation generated successfully.');
    }

    public function history(RecommendationHistoryFilterRequest $request): View
    {
        return view(
            'recommendation.history',
            $this->recommendationService->getHistoryPagePayload($request->validated()),
        );
    }

    public function show(Recommendation $recommendation): View
    {
        abort_if(
            $recommendation->user_id !== null && $recommendation->user_id !== auth()->id(),
            403
        );

        return view(
            'recommendation.show',
            $this->recommendationService->getRecommendationDetails($recommendation)
        );
    }
}
