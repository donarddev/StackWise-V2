<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\GoogleCallbackRequest;
use App\Http\Requests\Auth\GoogleRedirectRequest;
use App\Services\GoogleAuthService;
use Illuminate\Http\RedirectResponse;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(GoogleRedirectRequest $request, GoogleAuthService $googleAuthService): RedirectResponse
    {
        return $googleAuthService->redirect();
    }

    public function callback(GoogleCallbackRequest $request, GoogleAuthService $googleAuthService): RedirectResponse
    {
        try {
            $googleAuthService->authenticate();
            $request->session()->regenerate();

            return redirect()->route('dashboard');
        } catch (Throwable) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'google' => __('Google authentication failed. Please try again.'),
                ]);
        }
    }
}
