<?php

use App\Models\User;

test('guest navbar shows landing links and omits workspace chatbot url', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee(route('login'), false)
        ->assertSee(route('register'), false)
        ->assertSee(route('about'), false)
        ->assertSee(route('documentation.index'), false)
        ->assertDontSee(route('chatbot.index'), false);
});

test('authenticated navbar shows workspace links and omits landing-only routes', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee(route('dashboard'), false)
        ->assertSee(route('recommendation.create'), false)
        ->assertSee(route('recommendation.history'), false)
        ->assertSee(route('documentation.index'), false)
        ->assertSee(route('chatbot.index'), false)
        ->assertSee(route('logout'), false)
        ->assertDontSee(route('register'), false)
        ->assertDontSee(route('about'), false)
        ->assertDontSee(route('login'), false);
});

test('authenticated navbar submits logout with post method', function () {
    $user = User::factory()->create();

    $html = $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('method="POST"')
        ->and($html)->toContain(route('logout'));
});
