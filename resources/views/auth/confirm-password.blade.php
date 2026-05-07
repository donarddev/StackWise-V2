<x-guest-layout>
    <header class="mb-8 text-center">
        <h1 class="text-2xl font-semibold tracking-tight text-white sm:text-[1.65rem]">
            {{ __('Confirm your password') }}
        </h1>
        <p class="mt-2 text-sm leading-6 text-slate-400">
            {{ __('This is a secure area. Please confirm your password before continuing.') }}
        </p>
    </header>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

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

        <x-primary-button variant="guest" class="w-full sm:w-auto">
            {{ __('Confirm') }}
        </x-primary-button>
    </form>
</x-guest-layout>
