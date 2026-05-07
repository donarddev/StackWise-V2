<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Laravel'))</title>

        <link rel="icon" type="image/png" href="{{ asset('images/StackWise_Logo.png') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-100">
        <div class="relative isolate min-h-screen">
            <div
                class="pointer-events-none fixed inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,_rgba(16,185,129,0.14),_transparent_32%),radial-gradient(circle_at_top_right,_rgba(45,212,191,0.12),_transparent_28%),linear-gradient(180deg,_#020617_0%,_#081120_45%,_#020617_100%)]"
                aria-hidden="true"
            ></div>
            <div
                class="pointer-events-none fixed inset-0 z-0 opacity-60
                    [background-image:linear-gradient(rgba(148,163,184,0.14)_1px,transparent_1px),linear-gradient(90deg,rgba(148,163,184,0.14)_1px,transparent_1px)]
                    [background-size:44px_44px]
                    [mask-image:linear-gradient(to_bottom,rgba(0,0,0,0.9),rgba(0,0,0,0.35))]
                aria-hidden="true"
            ></div>

            <x-layout.navbar />

            @isset($header)
                <div class="border-b border-white/10 bg-slate-900/50">
                    <div class="mx-auto max-w-7xl px-4 py-6 text-white sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </div>
            @endisset

            <main class="relative pb-16 pt-2 sm:pb-20">
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot }}
                @endif
            </main>
        </div>
    </body>
</html>
