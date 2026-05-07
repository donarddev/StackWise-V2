<?php

use App\Models\User;

test('guests are redirected to login when visiting the recommendation form', function () {
    $this->get(route('recommendation.index', absolute: false))
        ->assertRedirect(route('login', absolute: false));
});

test('guests are redirected to login when visiting the alternate recommendation create url', function () {
    $this->get(route('recommendation.create', absolute: false))
        ->assertRedirect(route('login', absolute: false));
});

test('authenticated users can view the recommendation form', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('recommendation.index', absolute: false))
        ->assertOk();
});
