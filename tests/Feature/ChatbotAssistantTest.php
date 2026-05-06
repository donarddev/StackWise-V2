<?php

use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('stackwise assistant page loads with greeting', function () {
    get(route('chatbot.index'))
        ->assertOk()
        ->assertSee('Ask the StackWise Assistant', false)
        ->assertSee('Ask me about Python, Laravel, FastAPI, Agile, Waterfall', false);
});

test('stackwise assistant stores conversation in session after send', function () {
    post(route('chatbot.send'), [
        'message' => 'Tell me about Laravel',
    ])->assertRedirect(route('chatbot.index'));

    get(route('chatbot.index'))
        ->assertOk()
        ->assertSee('Tell me about Laravel', false)
        ->assertSee('Laravel is best for', false);
});

test('stackwise assistant clear resets session conversation', function () {
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
