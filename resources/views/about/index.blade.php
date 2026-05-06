@extends('layouts.app')

@section('title', 'About | StackWise AI')

@section('content')
    <section class="mx-auto max-w-4xl space-y-6">
        <h1 class="text-3xl font-bold tracking-tight text-white">About StackWise AI</h1>
        <p class="text-slate-300">
            StackWise AI is a student-friendly decision support system that recommends a programming language, development framework, and SDLC model based on project requirements.
        </p>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                <h2 class="font-semibold text-white">Architecture</h2>
                <p class="mt-2 text-sm text-slate-300">MVC for structure, Service Pattern for logic, and Form Request validation for clean controllers.</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                <h2 class="font-semibold text-white">Future Growth</h2>
                <p class="mt-2 text-sm text-slate-300">Recommendation history, feedback storage, database persistence, FastAPI integration, and Ollama chatbot support.</p>
            </div>
        </div>
    </section>
@endsection