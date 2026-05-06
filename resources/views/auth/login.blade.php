<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form x-data="{ showPassword: false }" method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold tracking-tight">Welcome back</h2>
            <p class="text-sm text-white/70">Log in to continue to {{ config('app.name') }}</p>
        </div>

        <div class="space-y-4">
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="mt-1" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" />
                <div class="relative mt-1">
                    <input id="password" name="password" required autocomplete="current-password" :type="showPassword ? 'text' : 'password'" class="block w-full px-4 py-3 bg-white/5 placeholder-gray-300 text-white rounded-xl border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition duration-150">
                    <button type="button" @click="showPassword = !showPassword" class="absolute right-2 top-1/2 -translate-y-1/2 text-sm text-white/60 hover:text-white">
                        <span x-text="showPassword ? 'Hide' : 'Show'"></span>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-white/20 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-white/80">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm text-white/70 hover:underline" href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
                @endif
            </div>

            <div class="flex items-center justify-end">
                <x-primary-button class="ms-3">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
        </div>
    </form>
</x-guest-layout>
