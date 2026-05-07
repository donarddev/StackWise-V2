<x-guest-layout>
    <header class="mb-8 text-center">
        <h1 class="text-2xl font-semibold tracking-tight text-white sm:text-[1.65rem]">
            {{ __('Set a new password') }}
        </h1>
        <p class="mt-2 text-sm leading-6 text-slate-400">
            {{ __('Choose a strong password for your StackWise account.') }}
        </p>
    </header>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" variant="guest" :value="__('Email')" />
            <x-text-input
                id="email"
                class="mt-2 block w-full"
                variant="guest"
                type="email"
                name="email"
                :value="old('email', $request->email)"
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
            {{ __('Reset Password') }}
        </x-primary-button>
    </form>
</x-guest-layout>
