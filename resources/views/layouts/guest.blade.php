<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('images/StackWise_Logo.png') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-100">
        <div class="relative isolate min-h-screen overflow-x-hidden">
            <div
                class="pointer-events-none fixed inset-0 -z-20 bg-[#020617]"
                aria-hidden="true"
            ></div>
            <div
                class="pointer-events-none fixed inset-0 -z-10 bg-[radial-gradient(ellipse_80%_50%_at_50%_-20%,rgba(16,185,129,0.18),transparent),radial-gradient(ellipse_60%_40%_at_100%_0%,rgba(45,212,191,0.12),transparent),radial-gradient(ellipse_50%_35%_at_0%_100%,rgba(16,185,129,0.08),transparent)]"
                aria-hidden="true"
            ></div>
            <div
                class="pointer-events-none fixed inset-0 -z-10 opacity-[0.35] [background-image:linear-gradient(rgba(148,163,184,0.07)_1px,transparent_1px),linear-gradient(90deg,rgba(148,163,184,0.07)_1px,transparent_1px)] [background-size:48px_48px]"
                aria-hidden="true"
            ></div>

            <div class="relative flex min-h-screen flex-col items-center justify-center px-4 py-10 sm:px-6 sm:py-12">
                <div
                    class="w-full max-w-md rounded-3xl border border-white/10 bg-slate-900/75 p-6 shadow-2xl shadow-slate-950/60 ring-1 ring-white/5 backdrop-blur-xl sm:p-8"
                >
                    <a
                        href="{{ route('home') }}"
                        class="mx-auto mb-8 flex w-fit justify-center rounded-2xl focus:outline-none focus:ring-2 focus:ring-emerald-400/50 focus:ring-offset-2 focus:ring-offset-slate-900"
                    >
                        <x-application-logo class="h-24 w-24 drop-shadow-[0_0_28px_rgba(16,185,129,0.22)]" />
                    </a>

                    {{ $slot }}
                </div>

                <p class="mt-8 max-w-md text-center text-xs text-slate-500">
                    <a
                        href="{{ route('home') }}"
                        class="text-slate-400 underline-offset-4 transition hover:text-emerald-300 hover:underline"
                    >
                        {{ __('Back to StackWise home') }}
                    </a>
                </p>
            </div>
        </div>
    </body>
</html>
