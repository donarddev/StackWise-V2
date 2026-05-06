<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'StackWise AI') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
        <div class="relative isolate min-h-screen overflow-hidden">
            <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,_rgba(34,197,94,0.18),_transparent_32%),radial-gradient(circle_at_top_right,_rgba(14,165,233,0.15),_transparent_28%),linear-gradient(180deg,_#020617_0%,_#0f172a_100%)]"></div>
            <div class="absolute left-1/2 top-0 -z-10 h-72 w-[42rem] -translate-x-1/2 rounded-full bg-emerald-400/10 blur-3xl"></div>

            <div class="flex items-center justify-center min-h-screen px-4 py-12">
                <div class="w-full max-w-md bg-white/5 backdrop-blur-md border border-white/5 rounded-2xl p-8 shadow-2xl">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
