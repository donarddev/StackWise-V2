<x-app-layout>
    <div class="relative isolate overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,_rgba(16,185,129,0.18),_transparent_28%),radial-gradient(circle_at_top_right,_rgba(45,212,191,0.14),_transparent_24%),linear-gradient(180deg,_#020617_0%,_#081120_42%,_#020617_100%)]"></div>
        <div class="absolute left-1/2 top-0 -z-10 h-80 w-[44rem] -translate-x-1/2 rounded-full bg-emerald-400/10 blur-3xl"></div>

        <section class="mx-auto max-w-7xl px-4 pb-16 pt-8 sm:px-6 lg:px-8 lg:pb-24 lg:pt-14">
            <div class="grid items-center gap-10 lg:grid-cols-[1.05fr_0.95fr]">
                <div class="space-y-8">
                    <x-ui.badge tone="teal">{{ $hero['badge'] }}</x-ui.badge>

                    <div class="space-y-5">
                        <h1 class="max-w-4xl text-4xl font-semibold tracking-tight text-white sm:text-5xl lg:text-6xl">
                            {{ $hero['headline'] }}
                        </h1>

                        <p class="max-w-2xl text-base leading-7 text-slate-300 sm:text-lg">
                            {{ $hero['description'] }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <x-ui.button-link :href="route('recommendation.index')" variant="primary">
                            Start Recommendation
                        </x-ui.button-link>

                        <x-ui.button-link :href="route('documentation.index')" variant="secondary">
                            Explore Documentation
                        </x-ui.button-link>
                    </div>

                    <p class="text-sm text-slate-400">
                        {{ $hero['supportingText'] }}
                    </p>

                    <div class="flex flex-wrap gap-3">
                        @foreach ($hero['highlights'] as $highlight)
                            <x-ui.badge tone="slate">{{ $highlight }}</x-ui.badge>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-[2rem] border border-white/10 bg-slate-900/70 p-6 shadow-2xl shadow-slate-950/40 ring-1 ring-inset ring-white/5 backdrop-blur-xl sm:p-8">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-400">Intelligence Overview</p>
                            <h2 class="mt-2 text-2xl font-semibold text-white">
                                {{ $decisionPanel['title'] }}
                            </h2>
                        </div>

                        <x-ui.badge tone="emerald">Live rules</x-ui.badge>
                    </div>

                    <p class="mt-4 max-w-xl text-sm leading-7 text-slate-300 sm:text-base">
                        {{ $decisionPanel['description'] }}
                    </p>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        @foreach ($decisionPanel['items'] as $item)
                            <x-ui.feature-card
                                :title="$item['title']"
                                :description="$item['description']"
                                :badge="$item['badge']"
                                :badge-tone="$item['badgeTone']"
                                compact
                            />
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
            <x-ui.section-heading
                :eyebrow="$process['eyebrow']"
                :title="$process['title']"
                :description="$process['description']"
            />

            <div class="mt-10 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($process['steps'] as $step)
                    <x-ui.process-step
                        :step="$step['step']"
                        :title="$step['title']"
                        :description="$step['description']"
                    />
                @endforeach
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
            <x-ui.section-heading
                :eyebrow="$modules['eyebrow']"
                :title="$modules['title']"
                :description="$modules['description']"
            />

            <div class="mt-10 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($modules['items'] as $module)
                    <x-ui.feature-card
                        :title="$module['title']"
                        :description="$module['description']"
                        :badge="$module['badge']"
                        :badge-tone="$module['badgeTone']"
                    />
                @endforeach
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
            <x-ui.section-heading
                :eyebrow="$benefits['eyebrow']"
                :title="$benefits['title']"
                :description="$benefits['description']"
            />

            <div class="mt-10 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($benefits['items'] as $benefit)
                    <x-ui.feature-card
                        :title="$benefit['title']"
                        :description="$benefit['description']"
                        :badge="$benefit['badge']"
                        :badge-tone="$benefit['badgeTone']"
                    />
                @endforeach
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-12 pb-20 sm:px-6 lg:px-8 lg:py-16 lg:pb-24">
            <div class="rounded-[2rem] border border-white/10 bg-gradient-to-br from-slate-900/80 to-slate-900/50 p-6 shadow-2xl shadow-slate-950/35 ring-1 ring-inset ring-white/5 sm:p-8 lg:p-10">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div class="max-w-2xl space-y-4">
                        <x-ui.badge tone="teal">Final step</x-ui.badge>
                        <h2 class="text-3xl font-semibold tracking-tight text-white sm:text-4xl">
                            {{ $cta['title'] }}
                        </h2>
                        <p class="text-base leading-7 text-slate-300 sm:text-lg">
                            {{ $cta['description'] }}
                        </p>
                    </div>

                    <x-ui.button-link :href="route('recommendation.index')" variant="primary" class="w-full lg:w-auto">
                        {{ $cta['button'] }}
                    </x-ui.button-link>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>