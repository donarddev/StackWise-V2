<?php

use App\Models\User;

test('about page can be rendered', function () {
    $this->get(route('about'))->assertOk();
});

test('guest about page shows guest CTA buttons', function () {
    $this->get(route('about'))
        ->assertOk()
        ->assertSee('Create Account', false)
        ->assertSee(route('register'), false)
        ->assertSee('Login', false)
        ->assertSee(route('login'), false);
});

test('authenticated about page shows generate CTA button', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('about'))
        ->assertOk()
        ->assertSee('Generate Recommendation', false)
        ->assertSee(route('recommendation.create'), false)
        ->assertDontSee('Create Account', false);
});
