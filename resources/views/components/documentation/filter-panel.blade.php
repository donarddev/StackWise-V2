@props([
    'filters' => ['search' => '', 'category' => 'all'],
])

@php
    $search = $filters['search'] ?? '';
    $category = $filters['category'] ?? 'all';
@endphp

<div class="rounded-3xl border border-white/10 bg-slate-900/40 p-5 shadow-xl shadow-slate-950/30">
    <form method="get" action="{{ route('documentation.index') }}" class="space-y-4">
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-[1.2fr_1fr_auto] lg:items-end">
            <div class="min-w-0">
                <label class="mb-2 block text-sm font-medium text-slate-200" for="documentation-search">Search topics</label>
                <input
                    id="documentation-search"
                    name="search"
                    type="search"
                    value="{{ $search }}"
                    autocomplete="off"
                    placeholder="Try “API”, “mobile”, “Agile”, or “beginner”"
                    class="w-full min-w-0 rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white placeholder:text-slate-500 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/20"
                />
            </div>

            <div class="min-w-0">
                <label class="mb-2 block text-sm font-medium text-slate-200" for="documentation-category">Category</label>
                <select
                    id="documentation-category"
                    name="category"
                    class="w-full min-w-0 rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/20"
                >
                    <option value="all" @selected($category === 'all')>All topics</option>
                    <option value="languages" @selected($category === 'languages')>Programming languages</option>
                    <option value="frameworks" @selected($category === 'frameworks')>Frameworks</option>
                    <option value="sdlc_models" @selected($category === 'sdlc_models')>SDLC models</option>
                </select>
            </div>

            <div class="flex flex-col gap-2 sm:flex-row lg:flex-col xl:flex-row">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-2xl bg-emerald-400 px-5 py-3 text-sm font-semibold text-slate-950 shadow-lg shadow-emerald-950/20 transition hover:bg-emerald-300"
                >
                    Apply filters
                </button>
            </div>
        </div>

        <p class="text-sm text-slate-400">
            Search by topic, use case, difficulty, or project type.
        </p>
    </form>
</div>
