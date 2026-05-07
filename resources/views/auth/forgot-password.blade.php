<x-guest-layout>
    <header class="mb-8 text-center">
        <h1 class="text-2xl font-semibold tracking-tight text-white sm:text-[1.65rem]">
            {{ __('Reset your password') }}
        </h1>
        <p class="mt-2 text-sm leading-6 text-slate-400">
            {{ __('Enter your email and we’ll send a password reset link.') }}
        </p>
    </header>

    <x-auth-session-status class="mb-4" variant="guest" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" variant="guest" :value="__('Email')" />
            <x-text-input
                id="email"
                class="mt-2 block w-full"
                variant="guest"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
            />
            <x-input-error class="mt-2" variant="guest" :messages="$errors->get('email')" />
        </div>

        <x-primary-button variant="guest" class="w-full sm:w-auto">
            {{ __('Email Password Reset Link') }}
        </x-primary-button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-400">
        <a
            href="{{ route('login') }}"
            class="font-medium text-emerald-300 underline-offset-4 transition hover:text-emerald-200 hover:underline"
        >
            {{ __('Back to log in') }}
        </a>
    </p>
</x-guest-layout>
