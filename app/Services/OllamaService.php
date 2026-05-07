<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OllamaService
{
    public function isConfigured(): bool
    {
        return (string) config('services.ollama.api_url', '') !== '';
    }

    /**
     * @param  list<array{role: string, content: string}>  $conversation
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    public function chat(string $message, array $conversation = []): string
    {
        $apiKey = (string) config('services.ollama.api_key');
        $apiUrl = (string) config('services.ollama.api_url');
        $model = (string) config('services.ollama.model');
        $timeout = (int) config('services.ollama.timeout', 30);

        $payload = $this->buildPayload(
            apiUrl: $apiUrl,
            model: $model,
            message: $message,
            conversation: $conversation,
        );

        $request = Http::connectTimeout(5)
            ->timeout($timeout)
            ->retry(2, 200)
            ->acceptJson()
            ->asJson()
            ->withHeaders($apiKey !== '' ? [
                'Authorization' => 'Bearer '.$apiKey,
            ] : []);

        $response = $request->post($apiUrl, $payload)->throw();

        $data = $response->json();
        $reply = $this->extractReply($data);

        if ($reply === null || trim($reply) === '') {
            throw new RuntimeException('Ollama response did not include a reply.');
        }

        return $reply;
    }

    /**
     * @param  list<array{role: string, content: string}>  $conversation
     * @return array<string, mixed>
     */
    private function buildPayload(string $apiUrl, string $model, string $message, array $conversation): array
    {
        if (str_contains($apiUrl, '/api/generate')) {
            return [
                'model' => $model,
                'prompt' => $this->buildPrompt($message, $conversation),
                'stream' => false,
            ];
        }

        return [
            'model' => $model,
            'messages' => [
                ...array_map(static fn (array $line): array => [
                    'role' => $line['role'],
                    'content' => $line['content'],
                ], $conversation),
                ['role' => 'user', 'content' => $message],
            ],
            'stream' => false,
        ];
    }

    /**
     * @param  list<array{role: string, content: string}>  $conversation
     */
    private function buildPrompt(string $message, array $conversation): string
    {
        $lines = [
            'You are the StackWise Assistant. Be concise, structured, and helpful for students choosing a tech stack and SDLC.',
            '',
        ];

        foreach ($conversation as $line) {
            $role = strtolower((string) ($line['role'] ?? 'user'));
            $content = trim((string) ($line['content'] ?? ''));
            if ($content === '') {
                continue;
            }

            $label = $role === 'assistant' ? 'Assistant' : 'User';
            $lines[] = "{$label}: {$content}";
        }

        $lines[] = 'User: '.trim($message);
        $lines[] = 'Assistant:';

        return implode("\n", $lines);
    }

    private function extractReply(mixed $data): ?string
    {
        if (! is_array($data)) {
            return null;
        }

        // Common shapes across chat APIs / proxies.
        if (is_string($data['reply'] ?? null)) {
            return $data['reply'];
        }

        if (is_string($data['response'] ?? null)) {
            return $data['response'];
        }

        if (is_string($data['content'] ?? null)) {
            return $data['content'];
        }

        if (is_array($data['message'] ?? null) && is_string($data['message']['content'] ?? null)) {
            return $data['message']['content'];
        }

        if (is_array($data['choices'] ?? null) && isset($data['choices'][0]) && is_array($data['choices'][0])) {
            $choice = $data['choices'][0];
            if (is_string($choice['message']['content'] ?? null)) {
                return $choice['message']['content'];
            }
        }

        return null;
    }
}
