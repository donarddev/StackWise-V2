<?php

use Illuminate\Support\Facades\Http;

use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('stackwise assistant page loads with greeting', function () {
    get(route('chatbot.index'))
        ->assertOk()
        ->assertSee('Ask the StackWise Assistant', false)
        ->assertSee('Ask me about your project', false);
});

test('stackwise assistant uses ollama when configured', function () {
    config()->set('services.ollama.api_url', 'http://ollama.test/api/chat');
    config()->set('services.ollama.api_key', '');
    config()->set('services.ollama.model', 'llama3.1');

    Http::fake([
        'http://ollama.test/api/chat' => Http::response([
            'message' => [
                'role' => 'assistant',
                'content' => 'This reply came from Ollama.',
            ],
        ]),
    ]);

    post(route('chatbot.send'), [
        'message' => 'Help me pick a stack for my project.',
    ])->assertRedirect(route('chatbot.index'));

    get(route('chatbot.index'))
        ->assertOk()
        ->assertSee('This reply came from Ollama.', false);
});

test('stackwise assistant stores conversation in session after send', function () {
    config()->set('services.ollama.api_url', '');

    post(route('chatbot.send'), [
        'message' => 'Tell me about Laravel',
    ])->assertRedirect(route('chatbot.index'));

    get(route('chatbot.index'))
        ->assertOk()
        ->assertSee('Tell me about Laravel', false)
        ->assertSee('The StackWise Assistant is not configured yet', false);
});

test('stackwise assistant clear resets session conversation', function () {
    config()->set('services.ollama.api_url', '');

    post(route('chatbot.send'), ['message' => 'What is Agile?']);
    post(route('chatbot.clear'))->assertRedirect(route('chatbot.index'));

    get(route('chatbot.index'))
        ->assertOk()
        ->assertDontSee('What is Agile?', false);
});

test('stackwise assistant requires message', function () {
    post(route('chatbot.send'), ['message' => ''])
        ->assertSessionHasErrors('message');
});
