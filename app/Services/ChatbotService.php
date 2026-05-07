<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Throwable;

class ChatbotService
{
    private const string SESSION_KEY = 'stackwise_assistant_conversation';

    private const int MAX_CONVERSATION_MESSAGES = 10;

    public function __construct(
        private readonly OllamaService $ollamaService,
    ) {}

    public function isAiEnabled(): bool
    {
        return $this->ollamaService->isConfigured();
    }

    /**
     * @return list<array{role: string, content: string}>
     */
    public function getConversation(): array
    {
        return session()->get(self::SESSION_KEY, []);
    }

    public function appendExchange(string $userMessage, string $assistantReply): void
    {
        /** @var list<array{role: string, content: string}> $conversation */
        $conversation = session()->get(self::SESSION_KEY, []);
        $conversation[] = ['role' => 'user', 'content' => $userMessage];
        $conversation[] = ['role' => 'assistant', 'content' => $assistantReply];

        if (count($conversation) > self::MAX_CONVERSATION_MESSAGES) {
            $conversation = array_slice($conversation, -self::MAX_CONVERSATION_MESSAGES);
        }

        session()->put(self::SESSION_KEY, $conversation);
    }

    public function clearConversation(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function generateResponse(string $message): string
    {
        $normalized = $this->normalize($message);

        if ($normalized === '') {
            return $this->fallbackResponse();
        }

        if (! $this->ollamaService->isConfigured()) {
            return 'The StackWise Assistant is not configured yet. Set `OLLAMA_API_URL` and `OLLAMA_MODEL` in your environment, then try again.';
        }

        try {
            return $this->ollamaService->chat($message, $this->getConversation());
        } catch (ConnectionException|RequestException|Throwable $e) {
            report($e);

            return 'I could not reach the Ollama API right now. Please make sure Ollama is running and `OLLAMA_API_URL` is correct, then try again.';
        }
    }

    public function getAssistantGreeting(): string
    {
        if (! $this->ollamaService->isConfigured()) {
            return "Hi, I'm the StackWise Assistant. To enable AI replies, configure `OLLAMA_API_URL` and `OLLAMA_MODEL` in your environment.";
        }

        return "Hi, I'm the StackWise Assistant. Ask me about your project and I’ll help you reason about a good stack and SDLC.";
    }

    /**
     * @return list<string>
     */
    public function getSuggestedQuestions(): array
    {
        return [
            'Suggest a stack for a student capstone web app (with auth + CRUD).',
            'What SDLC model fits a short timeline with changing requirements?',
            'Given these constraints, what language/framework should I pick?',
            'What are the trade-offs between Laravel and FastAPI for an API project?',
            'How should I plan my project roadmap for the next 8 weeks?',
        ];
    }

    /**
     * @return list<array{label: string, question: string}>
     */
    public function getTopicChips(): array
    {
        return [
            ['label' => 'Stack', 'question' => 'Recommend a stack based on my project requirements.'],
            ['label' => 'SDLC', 'question' => 'Which SDLC model fits my project best and why?'],
            ['label' => 'Roadmap', 'question' => 'Create a week-by-week roadmap for my project timeline.'],
            ['label' => 'Trade-offs', 'question' => 'Compare two frameworks for my use case and list trade-offs.'],
            ['label' => 'Risks', 'question' => 'What are the top risks for my project and how can I mitigate them?'],
        ];
    }

    /**
     * @return list<string>
     */
    public function getAssistantCapabilities(): array
    {
        return [
            'Explain programming languages',
            'Compare frameworks',
            'Explain SDLC models',
            'Clarify recommendation results',
            'Suggest beginner-friendly project direction',
            'Help draft a realistic project plan',
        ];
    }

    private function normalize(string $message): string
    {
        return strtolower(trim($message));
    }

    private function fallbackResponse(): string
    {
        return 'Please enter a message to start the conversation.';
    }
}
