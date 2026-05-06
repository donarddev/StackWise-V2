@props(['feedbacks', 'title' => 'Feedback'])

<article class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-slate-950/30">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-white">{{ $title }}</h2>
            <p class="mt-1 text-sm text-slate-400">Saved feedback entries linked to this recommendation.</p>
        </div>
        <span class="rounded-full border border-white/10 bg-slate-900/70 px-3 py-1 text-xs text-slate-300">{{ count($feedbacks) }} entries</span>
    </div>

    <div class="mt-4 space-y-3">
        @forelse ($feedbacks as $feedback)
            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-4">
                <div class="flex items-center justify-between gap-3">
                    <p class="font-semibold text-white">Rating: {{ $feedback['rating'] }}/5</p>
                    <span class="text-xs text-slate-400">{{ $feedback['created_at'] ? $feedback['created_at']->format('M d, Y') : 'Recently saved' }}</span>
                </div>
                @if (! empty($feedback['comment']))
                    <p class="mt-2 text-sm text-slate-300">{{ $feedback['comment'] }}</p>
                @else
                    <p class="mt-2 text-sm text-slate-400">No comment was provided.</p>
                @endif
            </div>
        @empty
            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-4 text-sm text-slate-400">
                No feedback has been submitted for this recommendation yet.
            </div>
        @endforelse
    </div>
</article>