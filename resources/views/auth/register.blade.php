<x-guest-layout>
    <header class="mb-8 text-center">
        <h1 class="text-2xl font-semibold tracking-tight text-white sm:text-[1.65rem]">
            {{ __('Create your StackWise account') }}
        </h1>
        <p class="mt-2 text-sm leading-6 text-slate-400">
            {{ __('Start generating explainable project stack recommendations.') }}
        </p>
    </header>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="name" variant="guest" :value="__('Name')" />
            <x-text-input
                id="name"
                class="mt-2 block w-full"
                variant="guest"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error class="mt-2" variant="guest" :messages="$errors->get('name')" />
        </div>

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
                autocomplete="new-password"
            />
            <x-input-error class="mt-2" variant="guest" :messages="$errors->get('password')" />
        </div>

        <div>
            <x-input-label for="password_confirmation" variant="guest" :value="__('Confirm Password')" />
            <x-text-input
                id="password_confirmation"
                class="mt-2 block w-full"
                variant="guest"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            />
            <x-input-error class="mt-2" variant="guest" :messages="$errors->get('password_confirmation')" />
        </div>

        <x-primary-button variant="guest" class="w-full sm:w-auto">
            {{ __('Create account') }}
        </x-primary-button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-400">
        {{ __('Already have an account?') }}
        <a
            href="{{ route('login') }}"
            class="font-medium text-emerald-300 underline-offset-4 transition hover:text-emerald-200 hover:underline"
        >
            {{ __('Log in') }}
        </a>
    </p>
</x-guest-layout>
