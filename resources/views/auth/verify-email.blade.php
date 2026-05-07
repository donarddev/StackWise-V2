<x-guest-layout>
    <header class="mb-8 text-center">
        <h1 class="text-2xl font-semibold tracking-tight text-white sm:text-[1.65rem]">
            {{ __('Verify your email') }}
        </h1>
        <p class="mt-2 text-sm leading-6 text-slate-400">
            {{ __('Thanks for signing up! Before getting started, please verify your email by clicking the link we sent. If you didn’t receive it, we can send another.') }}
        </p>
    </header>

    @if (session('status') == 'verification-link-sent')
        <div
            class="mb-6 rounded-xl border border-emerald-400/25 bg-emerald-400/10 px-3 py-2 text-sm font-medium text-emerald-200"
            role="status"
        >
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button variant="guest" class="w-full sm:w-auto">
                {{ __('Resend Verification Email') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="w-full text-center text-sm text-slate-400 underline-offset-4 transition hover:text-emerald-300 hover:underline focus:outline-none focus:ring-2 focus:ring-emerald-400/40 focus:ring-offset-2 focus:ring-offset-slate-900 rounded sm:w-auto"
            >
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
