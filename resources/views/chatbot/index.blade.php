@extends('layouts.app')

@section('title', 'StackWise Assistant | StackWise AI')

@section('content')
    @php
        $conversation = $conversation ?? [];
        $assistantGreeting = $assistantGreeting ?? '';
        $suggestedQuestions = $suggestedQuestions ?? [];
        $topicChips = $topicChips ?? [];
        $assistantCapabilities = $assistantCapabilities ?? [];
        $aiEnabled = $aiEnabled ?? false;
    @endphp

    <section class="mx-auto max-w-7xl space-y-8">
        <x-chatbot.page-hero
            title="Ask the StackWise Assistant"
            description="Get quick guidance about programming languages, frameworks, SDLC models, and project stack recommendations."
            note="{{ $aiEnabled ? 'This assistant is powered by Ollama via API. If the model is offline, you will see an error message instead of a reply.' : 'AI replies are currently disabled. Configure OLLAMA_API_URL and OLLAMA_MODEL to enable Ollama-powered responses.' }}"
        />

        <div class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
            <div class="flex min-h-0 flex-col rounded-3xl border border-white/10 bg-slate-900/35 p-6 shadow-2xl shadow-slate-950/30">
                <div class="flex flex-wrap items-start justify-between gap-4 border-b border-white/10 pb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-white">Conversation</h2>
                        <p class="mt-1 text-sm text-slate-400">{{ $aiEnabled ? 'Ollama-powered StackWise Assistant' : 'Ollama not configured (AI disabled)' }}</p>
                    </div>
                    <x-ui.badge :tone="$aiEnabled ? 'emerald' : 'amber'">{{ $aiEnabled ? 'AI enabled' : 'AI disabled' }}</x-ui.badge>
                </div>

                <div
                    id="chat-messages"
                    class="mt-5 flex max-h-[min(28rem,70vh)] min-h-[12rem] flex-col gap-4 overflow-y-auto overflow-x-hidden rounded-2xl border border-white/10 bg-slate-950/50 p-4 pr-3 scroll-smooth"
                    aria-live="polite"
                >
                    <x-chatbot.chat-bubble role="assistant" :content="$assistantGreeting" />

                    @foreach ($conversation as $line)
                        <x-chatbot.chat-bubble :role="$line['role']" :content="$line['content']" />
                    @endforeach

                    <div id="chat-bottom" aria-hidden="true"></div>
                </div>

                <form id="chat-form" method="POST" action="{{ route('chatbot.send') }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label for="chat-message" class="mb-2 block text-sm font-medium text-slate-200">Your message</label>
                        <textarea
                            id="chat-message"
                            name="message"
                            rows="4"
                            required
                            class="min-h-[6rem] w-full min-w-0 rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white placeholder:text-slate-500 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/20"
                            placeholder="Ask about stacks, SDLC, or StackWise AI recommendations…"
                        >{{ old('message') }}</textarea>
                        @error('message')
                            <div class="mt-3">
                                <x-ui.alert type="error" :message="$message" />
                            </div>
                        @enderror
                    </div>

                    <div>
                        <p class="mb-2 text-xs font-medium uppercase tracking-wider text-slate-500">Quick topics</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($topicChips as $chip)
                                <x-chatbot.topic-chip :label="$chip['label']" :question="$chip['question']" />
                            @endforeach
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-full bg-emerald-400 px-6 py-3 text-sm font-semibold text-slate-950 shadow-lg shadow-emerald-950/20 transition hover:bg-emerald-300"
                        >
                            Send message
                        </button>

                        <button
                            type="submit"
                            formaction="{{ route('chatbot.clear') }}"
                            formnovalidate
                            class="inline-flex items-center justify-center rounded-full border border-white/15 bg-transparent px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10"
                        >
                            Clear chat
                        </button>
                    </div>
                </form>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl border border-white/10 bg-slate-900/35 p-6 shadow-2xl shadow-slate-950/30">
                    <h2 class="text-lg font-semibold text-white">Suggested questions</h2>
                    <p class="mt-1 text-sm text-slate-400">Tap to send instantly (same as typing and submitting).</p>
                    <div class="mt-4 space-y-2">
                        @foreach ($suggestedQuestions as $question)
                            <x-chatbot.suggested-question-button :question="$question" />
                        @endforeach
                    </div>
                </div>

                <x-chatbot.capability-panel :items="$assistantCapabilities" />
            </div>
        </div>
    </section>

    <script>
        (function () {
            const form = document.getElementById('chat-form');
            const input = document.getElementById('chat-message');
            const messages = document.getElementById('chat-messages');
            const scrollToBottom = function () {
                if (!messages) {
                    return;
                }

                requestAnimationFrame(function () {
                    messages.scrollTop = messages.scrollHeight;
                });
            };

            document.addEventListener('DOMContentLoaded', function () {
                scrollToBottom();
                setTimeout(scrollToBottom, 50);
            });

            if (!form || !input) {
                return;
            }

            window.stackwiseFillChat = function (q) {
                input.value = q;
                input.focus();
            };
            window.stackwiseSendChat = function (q) {
                input.value = q;
                scrollToBottom();
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    form.submit();
                }
            };
        })();
    </script>
@endsection
