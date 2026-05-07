<x-guest-layout>
    <header class="mb-8 text-center">
        <h1 class="text-2xl font-semibold tracking-tight text-white sm:text-[1.65rem]">
            {{ __('Welcome back') }}
        </h1>
        <p class="mt-2 text-sm leading-6 text-slate-400">
            {{ __('Continue your StackWise project decision workspace.') }}
        </p>
    </header>

    <x-auth-session-status class="mb-4" variant="guest" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
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
                autocomplete="username"
            />
            <x-input-error class="mt-2" variant="guest" :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="password" variant="guest" :value="__('Password')" />
            <x-text-input
                id="password"
                class="mt-2 block w-full"
                variant="guest"
                type="password"
                name="password"
                required
                autocomplete="current-password"
            />
            <x-input-error class="mt-2" variant="guest" :messages="$errors->get('password')" />
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex cursor-pointer items-center">
                <input
                    id="remember_me"
                    type="checkbox"
                    class="rounded border-white/20 bg-slate-950/50 text-emerald-400 shadow-sm focus:ring-2 focus:ring-emerald-400/40"
                    name="remember"
                >
                <span class="ms-2 text-sm text-slate-400">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a
                    class="text-sm text-slate-400 underline-offset-4 transition hover:text-emerald-300 hover:underline focus:outline-none focus:ring-2 focus:ring-emerald-400/40 focus:ring-offset-2 focus:ring-offset-slate-900 rounded"
                    href="{{ route('password.request') }}"
                >
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <x-primary-button variant="guest" class="w-full sm:w-auto">
            {{ __('Log in') }}
        </x-primary-button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-400">
        {{ __('New to StackWise?') }}
        <a
            href="{{ route('register') }}"
            class="font-medium text-emerald-300 underline-offset-4 transition hover:text-emerald-200 hover:underline"
        >
            {{ __('Create an account') }}
        </a>
    </p>
</x-guest-layout>
