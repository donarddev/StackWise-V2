<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecommendationHistoryFilterRequest;
use App\Http\Requests\RecommendationRequest;
use App\Services\RecommendationFormService;
use App\Services\RecommendationService;
use Illuminate\Contracts\View\View;

class RecommendationController extends Controller
{
    public function __construct(
        private readonly RecommendationFormService $recommendationFormService,
        private readonly RecommendationService $recommendationService,
    ) {
    }

    public function index(): View
    {
        $formData = $this->recommendationFormService->getFormPageData();
        // Remove 'header' to prevent conflict with layout's $header variable
        $header = $formData['header'];
        unset($formData['header']);
        $formData['pageHeader'] = $header;

        return view('recommendation.create', $formData);
    }

    public function generate(RecommendationRequest $request): View
    {
        $validated = $request->validated();

        $result = $this->recommendationService->generateAndStoreRecommendation($validated);

        return view('recommendation.result', [
            'recommendation' => $result['report'],
            'recommendationRecord' => $result['record'],
        ]);
    }

    public function history(RecommendationHistoryFilterRequest $request): View
    {
        return view(
            'recommendation.history',
            $this->recommendationService->getHistoryPagePayload($request->validated()),
        );
    }

    public function show(string $recommendation): View
    {
        return view('recommendation.show', $this->recommendationService->getRecommendationDetails((int) $recommendation));
    }
}
