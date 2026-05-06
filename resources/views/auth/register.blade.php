<x-guest-layout>
    <form x-data="{ showPassword: false, showConfirm: false }" method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold tracking-tight">Create your account</h2>
            <p class="text-sm text-white/70">Join {{ config('app.name') }} — get personalized recommendations fast.</p>
        </div>

        <div class="space-y-4">
            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" class="mt-1" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="mt-1" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" />
                <div class="relative mt-1">
                    <input id="password" name="password" required autocomplete="new-password" :type="showPassword ? 'text' : 'password'" class="block w-full px-4 py-3 bg-white/5 placeholder-gray-300 text-white rounded-xl border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition duration-150">
                    <button type="button" @click="showPassword = !showPassword" class="absolute right-2 top-1/2 -translate-y-1/2 text-sm text-white/60 hover:text-white">
                        <span x-text="showPassword ? 'Hide' : 'Show'"></span>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <div class="relative mt-1">
                    <input id="password_confirmation" name="password_confirmation" required autocomplete="new-password" :type="showConfirm ? 'text' : 'password'" class="block w-full px-4 py-3 bg-white/5 placeholder-gray-300 text-white rounded-xl border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition duration-150">
                    <button type="button" @click="showConfirm = !showConfirm" class="absolute right-2 top-1/2 -translate-y-1/2 text-sm text-white/60 hover:text-white">
                        <span x-text="showConfirm ? 'Hide' : 'Show'"></span>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between">
                <a class="text-sm text-white/70 hover:underline" href="{{ route('login') }}">{{ __('Already registered?') }}</a>

                <x-primary-button class="ms-4">
                    {{ __('Register') }}
                </x-primary-button>
            </div>
        </div>
    </form>
</x-guest-layout>
