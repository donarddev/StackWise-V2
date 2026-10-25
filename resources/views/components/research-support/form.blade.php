@props([
    'recommendation',
    'submitLabel',
    'researchSupport' => null,
])

<form
    method="POST"
    action="{{ route('recommendations.research-support.store', $recommendation) }}"
    class="mt-6 space-y-4 rounded-2xl border border-white/10 bg-slate-900/70 p-5"
>
    @csrf

    <x-ui.form-field name="study_title" label="Study Title (optional)" hint="Optional working title for your research study.">
        <x-ui.form-input
            name="study_title"
            :value="old('study_title', $researchSupport?->study_title)"
            placeholder="e.g. A Web-Based Portal for Student Project Management"
        />
    </x-ui.form-field>

    <x-ui.form-field name="study_context" label="Study Context (optional)" hint="Optional notes, scope, or focus areas for the research draft.">
        <x-ui.form-textarea
            name="study_context"
            :value="old('study_context', $researchSupport?->study_context)"
            rows="4"
            placeholder="Describe the research focus, target users, or modules you want emphasized."
        />
    </x-ui.form-field>

    <div class="flex flex-wrap gap-3 pt-2">
        <x-ui.button type="submit">
            {{ $submitLabel }}
        </x-ui.button>
    </div>
</form>
