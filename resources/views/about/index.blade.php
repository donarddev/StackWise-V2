@extends('layouts.app')

@section('title', 'About | StackWise AI')

@section('content')
    <div class="relative isolate">
        <div class="absolute inset-x-0 -top-24 -z-10 h-80 bg-[radial-gradient(circle_at_top,_rgba(16,185,129,0.16),_transparent_55%)] blur-3xl"></div>

        <section class="mx-auto max-w-7xl space-y-16 px-4 sm:px-6 lg:px-8">
            <x-ui.page-header
                :badge="$hero['badge']"
                :title="$hero['title']"
                :description="$hero['description']"
            />

            <div class="grid gap-4 md:grid-cols-3">
                @foreach ($hero['highlights'] as $highlight)
                    <x-ui.trust-card :title="$highlight['title']" :description="$highlight['description']" />
                @endforeach
            </div>

            <section class="space-y-6">
                <x-ui.section-heading
                    eyebrow="Context"
                    title="Problem and solution"
                    description="Why StackWise AI exists and how it supports student projects."
                />

                <div class="grid gap-4 lg:grid-cols-2">
                    <x-ui.feature-card
                        :title="$problemSolution['problem']['title']"
                        :description="$problemSolution['problem']['content']"
                        badge="Context"
                        badgeTone="slate"
                    />

                    <x-ui.feature-card
                        :title="$problemSolution['solution']['title']"
                        :description="$problemSolution['solution']['content']"
                        badge="Approach"
                        badgeTone="emerald"
                    />
                </div>
            </section>

            <section class="space-y-6">
                <x-ui.section-heading
                    :eyebrow="$process['eyebrow']"
                    :title="$process['title']"
                    :description="$process['description']"
                />

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    @foreach ($process['steps'] as $step)
                        <x-ui.process-step :step="$step['step']" :title="$step['title']" :description="$step['description']" />
                    @endforeach
                </div>
            </section>

            <section class="space-y-6">
                <x-ui.section-heading
                    :eyebrow="$features['eyebrow']"
                    :title="$features['title']"
                    :description="$features['description']"
                />

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($features['items'] as $feature)
                        <x-ui.feature-card
                            :title="$feature['title']"
                            :description="$feature['description']"
                            :badge="$feature['badge']"
                            :badgeTone="$feature['badgeTone']"
                            compact
                        />
                    @endforeach
                </div>
            </section>

            <section class="space-y-6">
                <x-ui.section-heading
                    :eyebrow="$architecture['eyebrow']"
                    :title="$architecture['title']"
                    :description="$architecture['description']"
                />

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($architecture['layers'] as $layer)
                        <x-ui.guidance-item :title="$layer['title']" :description="$layer['description']" />
                    @endforeach
                </div>
            </section>

            <section class="space-y-6">
                <x-ui.section-heading
                    :eyebrow="$output['eyebrow']"
                    :title="$output['title']"
                    :description="$output['description']"
                />

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($output['items'] as $item)
                        <x-ui.guidance-item :title="$item['title']" :description="$item['description']" />
                    @endforeach
                </div>
            </section>

            <section class="space-y-6">
                <x-ui.section-heading
                    :eyebrow="$futureGrowth['eyebrow']"
                    :title="$futureGrowth['title']"
                    :description="$futureGrowth['description']"
                />

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($futureGrowth['items'] as $item)
                        <x-ui.feature-card
                            :title="$item['title']"
                            :description="$item['description']"
                            :badge="$item['badge']"
                            :badgeTone="$item['badgeTone']"
                            compact
                        />
                    @endforeach
                </div>
            </section>

            <section class="pb-12">
                <div class="relative overflow-hidden rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-2xl shadow-slate-950/40 backdrop-blur sm:p-10">
                    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(16,185,129,0.16),_transparent_55%),radial-gradient(circle_at_bottom_right,_rgba(45,212,191,0.12),_transparent_55%)]"></div>
                    <div class="relative grid gap-6 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
                        <div class="space-y-3">
                            <x-ui.badge tone="teal">Next step</x-ui.badge>
                            <h2 class="text-3xl font-semibold tracking-tight text-white">{{ $cta['title'] }}</h2>
                            <p class="max-w-2xl text-sm leading-7 text-slate-300 sm:text-base">{{ $cta['description'] }}</p>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:justify-end">
                            @guest
                                <x-ui.button-link :href="route('register')" variant="primary">
                                    Create Account
                                </x-ui.button-link>
                                <x-ui.button-link :href="route('login')" variant="secondary">
                                    Login
                                </x-ui.button-link>
                            @endguest

                            @auth
                                <x-ui.button-link :href="route('recommendation.create')" variant="primary">
                                    Generate Recommendation
                                </x-ui.button-link>
                            @endauth
                        </div>
                    </div>
                </div>
            </section>
        </section>
    </div>
@endsection