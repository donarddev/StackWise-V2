<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatbotRequest;
use App\Services\ChatbotService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ChatbotController extends Controller
{
    public function __construct(
        private readonly ChatbotService $chatbotService,
    ) {}

    public function index(): View
    {
        return view('chatbot.index', [
            'conversation' => $this->chatbotService->getConversation(),
            'assistantGreeting' => $this->chatbotService->getAssistantGreeting(),
            'suggestedQuestions' => $this->chatbotService->getSuggestedQuestions(),
            'topicChips' => $this->chatbotService->getTopicChips(),
            'assistantCapabilities' => $this->chatbotService->getAssistantCapabilities(),
            'aiEnabled' => $this->chatbotService->isAiEnabled(),
        ]);
    }

    public function send(ChatbotRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $response = $this->chatbotService->generateResponse($validated['message']);
        $this->chatbotService->appendExchange($validated['message'], $response);

        return redirect()->route('chatbot.index');
    }

    public function clear(): RedirectResponse
    {
        $this->chatbotService->clearConversation();

        return redirect()->route('chatbot.index');
    }
}
