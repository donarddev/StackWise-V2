@extends('layouts.app')

@section('title', 'Recommendation Form | StackWise AI')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <div class="grid gap-8 lg:grid-cols-[minmax(0,1.5fr)_minmax(20rem,0.85fr)] lg:items-start">
            <div class="space-y-8">
                <x-ui.page-header
                    :badge="$pageHeader['badge']"
                    :title="$pageHeader['title']"
                    :description="$pageHeader['description']"
                />

                @if ($errors->any())
                    <x-ui.alert type="error" :message="$errorNotice" />
                @endif

                <form method="POST" action="{{ route('recommendation.generate') }}" class="space-y-6">
                    @csrf

                    @foreach ($sections as $section)
                        <x-ui.form-section-card :step="$section['step']" :title="$section['title']" :description="$section['description']">
                            @if (isset($section['summary']))
                                <div class="grid gap-4 lg:grid-cols-[minmax(0,1.3fr)_minmax(18rem,0.7fr)] lg:items-center">
                                    <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                                        <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-400">Summary</p>
                                        <h3 class="mt-2 text-xl font-semibold text-white">{{ $section['summary']['title'] }}</h3>
                                        <p class="mt-3 text-sm leading-7 text-slate-300">{{ $section['summary']['description'] }}</p>
                                    </div>

                                    <div class="rounded-3xl border border-emerald-400/15 bg-emerald-400/10 p-5 text-sm leading-7 text-emerald-100">
                                        This final report is designed to be readable in class, shareable with teammates, and easy to revisit later.
                                    </div>
                                </div>
                            @else
                                <div class="{{ $section['gridClass'] }}">
                                    @foreach ($section['fields'] as $field)
                                        <div class="{{ $field['columnSpan'] ?? '' }}">
                                            <x-ui.form-field :name="$field['name']" :label="$field['label']" :hint="$field['hint'] ?? null" required>
                                                @if ($field['type'] === 'text' || $field['type'] === 'number')
                                                    <x-ui.form-input
                                                        :name="$field['name']"
                                                        :type="$field['type']"
                                                        :value="old($field['name'])"
                                                        :placeholder="$field['placeholder'] ?? null"
                                                        :min="$field['min'] ?? null"
                                                    />
                                                @elseif ($field['type'] === 'select')
                                                    <x-ui.form-select
                                                        :name="$field['name']"
                                                        :placeholder="$field['placeholder'] ?? null"
                                                        :options="$field['options']"
                                                    />
                                                @elseif ($field['type'] === 'multi_select')
                                                    <x-ui.form-multi-select
                                                        :name="$field['name']"
                                                        :placeholder="$field['placeholder'] ?? null"
                                                        :options="$field['options']"
                                                        class="min-h-[11rem]"
                                                    />
                                                @elseif ($field['type'] === 'textarea')
                                                    <x-ui.form-textarea
                                                        :name="$field['name']"
                                                        :value="old($field['name'])"
                                                        :placeholder="$field['placeholder'] ?? null"
                                                        :rows="$field['rows'] ?? 5"
                                                    />
                                                @endif
                                            </x-ui.form-field>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </x-ui.form-section-card>
                    @endforeach

                    <div class="grid gap-4 md:grid-cols-3">
                        @foreach ($trustCards as $trustCard)
                            <x-ui.trust-card :title="$trustCard['title']" :description="$trustCard['description']" />
                        @endforeach
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <x-ui.button type="submit" variant="primary" class="w-full sm:w-auto">
                            Generate Recommendation
                        </x-ui.button>

                        <x-ui.button-link :href="route('home')" variant="secondary" class="w-full sm:w-auto">
                            Back to Home
                        </x-ui.button-link>
                    </div>
                </form>
            </div>

            <aside class="space-y-6 lg:sticky lg:top-6">
                <div class="rounded-[2rem] border border-white/10 bg-slate-900/70 p-6 shadow-2xl shadow-slate-950/30 ring-1 ring-inset ring-white/5">
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <x-ui.badge tone="teal">Guidance panel</x-ui.badge>
                            <h2 class="text-2xl font-semibold tracking-tight text-white">What StackWise AI analyzes</h2>
                            <p class="text-sm leading-7 text-slate-300">These inputs help the engine compare the project context before generating a recommendation report.</p>
                        </div>

                        <div class="space-y-3">
                            @foreach ($guidance as $item)
                                <x-ui.guidance-item :title="$item['title']" :description="$item['description']" />
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-white/10 bg-gradient-to-br from-slate-900/80 to-slate-900/50 p-6 shadow-2xl shadow-slate-950/30 ring-1 ring-inset ring-white/5">
                    <div class="space-y-4">
                        <x-ui.badge tone="emerald">Submission flow</x-ui.badge>
                        <h3 class="text-xl font-semibold text-white">Before you submit</h3>
                        <p class="text-sm leading-7 text-slate-300">StackWise AI will store the recommendation, show the result, and keep the generated report available for future review.</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>
@endsection