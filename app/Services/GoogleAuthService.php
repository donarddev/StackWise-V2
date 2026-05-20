<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use RuntimeException;

class GoogleAuthService
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function authenticate(): User
    {
        $googleUser = Socialite::driver('google')->user();

        return DB::transaction(function () use ($googleUser): User {
            $user = $this->findOrCreateUser($googleUser);

            Auth::login($user, true);

            return $user;
        });
    }

    private function findOrCreateUser(SocialiteUser $googleUser): User
    {
        $googleId = $googleUser->getId();
        $email = $googleUser->getEmail();

        if (! is_string($googleId) || $googleId === '' || ! is_string($email) || $email === '') {
            throw new RuntimeException('Google account did not return a usable id and email.');
        }

        $email = Str::lower($email);

        $user = User::query()
            ->where('google_id', $googleId)
            ->first();

        if ($user instanceof User) {
            $user->forceFill([
                'avatar' => $googleUser->getAvatar(),
            ])->save();

            return $user;
        }

        $user = User::query()
            ->where('email', $email)
            ->first();

        if ($user instanceof User) {
            $user->forceFill([
                'google_id' => $googleId,
                'avatar' => $googleUser->getAvatar(),
            ])->save();

            return $user;
        }

        return User::create([
            'name' => $googleUser->getName() ?: Str::before($email, '@'),
            'email' => $email,
            'google_id' => $googleId,
            'avatar' => $googleUser->getAvatar(),
            'password' => Hash::make(Str::random(32)),
        ]);
    }
}
