@props(['recommendationId' => null, 'successMessage' => null, 'action' => null])

<article class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-slate-950/30">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-white">Feedback</h2>
            <p class="mt-1 text-sm text-slate-400">Share how helpful this recommendation was.</p>
        </div>
        <span class="rounded-full border border-white/10 bg-slate-900/70 px-3 py-1 text-xs text-slate-300">Optional comment</span>
    </div>

    @if ($successMessage)
        <div class="mt-4">
            <x-ui.alert type="success" :message="$successMessage" />
        </div>
    @endif

    <form method="POST" action="{{ $action ?? route('feedback.store') }}" class="mt-5 space-y-4">
        @csrf

        @if ($recommendationId)
            <input type="hidden" name="recommendation_id" value="{{ $recommendationId }}">
        @else
            <input type="hidden" name="recommendation_id" value="">
        @endif

        <div>
            <label for="rating" class="mb-2 block text-sm font-medium text-slate-200">Rating</label>
            <select id="rating" name="rating" class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/20">
                <option value="">Select a rating</option>
                @for ($rating = 1; $rating <= 5; $rating++)
                    <option value="{{ $rating }}" @selected((string) old('rating') === (string) $rating)>
                        {{ $rating }}
                    </option>
                @endfor
            </select>
            @error('rating')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="comment" class="mb-2 block text-sm font-medium text-slate-200">Comment</label>
            <textarea id="comment" name="comment" rows="4" class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white placeholder:text-slate-500 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/20" placeholder="Share a short comment about the recommendation.">{{ old('comment') }}</textarea>
            @error('comment')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="rounded-full bg-emerald-400 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-emerald-300">
                Submit Feedback
            </button>
        </div>
    </form>
</article>
