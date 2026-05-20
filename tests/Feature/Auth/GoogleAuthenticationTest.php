<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

function googleUser(array $overrides = []): SocialiteUser
{
    return (new SocialiteUser)->map(array_merge([
        'id' => 'google-123',
        'name' => 'Google User',
        'email' => 'google@example.com',
        'avatar' => 'https://example.com/avatar.png',
    ], $overrides));
}

test('guests can be redirected to Google', function () {
    Socialite::fake('google');

    $this->get(route('google.redirect', absolute: false))
        ->assertRedirect('https://socialite.fake/google/authorize');
});

test('google callback creates a new user and signs them in', function () {
    Socialite::fake('google', googleUser());

    $this->get(route('google.callback', absolute: false))
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'name' => 'Google User',
        'email' => 'google@example.com',
        'google_id' => 'google-123',
        'avatar' => 'https://example.com/avatar.png',
    ]);
    $this->assertDatabaseCount('recommendations', 0);
});

test('google callback links an existing user by email', function () {
    $user = User::factory()->create([
        'email' => 'google@example.com',
        'google_id' => null,
        'avatar' => null,
        'password' => Hash::make('password'),
    ]);

    Socialite::fake('google', googleUser([
        'id' => 'google-linked-123',
        'avatar' => 'https://example.com/linked-avatar.png',
    ]));

    $this->get(route('google.callback', absolute: false))
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user->fresh());
    $this->assertDatabaseCount('users', 1);
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'email' => 'google@example.com',
        'google_id' => 'google-linked-123',
        'avatar' => 'https://example.com/linked-avatar.png',
    ]);
});

test('google callback failure redirects back to login with an error', function () {
    Socialite::fake('google', fn () => throw new RuntimeException('Google failed.'));

    $this->get(route('google.callback', absolute: false))
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors([
            'google' => 'Google authentication failed. Please try again.',
        ]);

    $this->assertGuest();
});
