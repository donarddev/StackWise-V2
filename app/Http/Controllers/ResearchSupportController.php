<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateResearchSupportRequest;
use App\Models\Recommendation;
use App\Services\ResearchSupportService;
use Illuminate\Http\RedirectResponse;

class ResearchSupportController extends Controller
{
    public function __construct(
        private readonly ResearchSupportService $researchSupportService,
    ) {}

    public function store(GenerateResearchSupportRequest $request, Recommendation $recommendation): RedirectResponse
    {
        abort_if(
            $recommendation->user_id !== null && $recommendation->user_id !== $request->user()->id,
            403
        );

        $result = $this->researchSupportService->generate(
            $request->user(),
            $recommendation,
            $request->validated(),
        );

        $redirect = redirect()
            ->to(route('recommendation.show', $recommendation).'#research-support')
            ->with('success', 'Research support generated successfully.');

        if ($result->usedFallback) {
            $redirect->with('warning', 'Ollama was not reachable, so a basic research support template was generated.');
        }

        return $redirect;
    }
}
