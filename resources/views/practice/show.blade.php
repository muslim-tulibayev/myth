@extends('layouts.app')

@section('content')

<div
    x-data="practice({{ $cards->toJson() }})"
    class="max-w-xl mx-auto"
>

    {{-- Mode selection --}}
    <template x-if="mode === null">
        <div class="bg-white rounded-xl border border-gray-200 px-8 py-10 text-center">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">{{ $collection->name }}</p>
            <h2 class="text-xl font-semibold text-gray-900 mb-1">Practice all cards</h2>
            <p class="text-sm text-gray-500 mb-8" x-text="cards.length + ' card' + (cards.length !== 1 ? 's' : '')"></p>

            <div class="flex gap-3 justify-center">
                <button @click="startSession('passive')"
                        class="flex-1 max-w-44 bg-gray-900 text-white text-sm font-medium px-5 py-3 rounded-lg hover:bg-gray-700 transition-colors">
                    Passive
                    <span class="block text-xs font-normal text-gray-400 mt-0.5">Reveal &amp; self-grade</span>
                </button>
                <button @click="startSession('variants')"
                        class="flex-1 max-w-44 bg-indigo-600 text-white text-sm font-medium px-5 py-3 rounded-lg hover:bg-indigo-500 transition-colors">
                    Variants
                    <span class="block text-xs font-normal text-indigo-300 mt-0.5">Multiple choice</span>
                </button>
            </div>

            <a href="{{ route('collections.show', $collection) }}"
               class="inline-block mt-6 text-sm text-gray-400 hover:text-gray-600 transition-colors">
                Cancel
            </a>
        </div>
    </template>

    {{-- Session complete --}}
    <template x-if="done">
        <div class="bg-white rounded-xl border border-gray-200 px-8 py-10 text-center">
            <p class="text-3xl mb-4">🎉</p>
            <h2 class="text-xl font-semibold text-gray-900 mb-2">Practice complete</h2>
            <p class="text-sm text-gray-500 mb-6">{{ $collection->name }}</p>

            <div class="flex justify-center gap-8 mb-8">
                <div>
                    <p class="text-2xl font-bold text-green-600" x-text="correct"></p>
                    <p class="text-xs text-gray-400 mt-1">Correct</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-red-500" x-text="incorrect"></p>
                    <p class="text-xs text-gray-400 mt-1">Missed</p>
                </div>
            </div>

            <a href="{{ route('collections.show', $collection) }}"
               class="bg-gray-900 text-white text-sm font-medium px-6 py-2.5 rounded-lg hover:bg-gray-700 transition-colors">
                Back to collection
            </a>
        </div>
    </template>

    {{-- Card review --}}
    <template x-if="mode !== null && !done">
        <div>
            {{-- Progress bar --}}
            <div class="flex items-center gap-3 mb-5">
                <div class="flex-1 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-indigo-500 rounded-full transition-all duration-300"
                         :style="'width: ' + Math.round((index / cards.length) * 100) + '%'"></div>
                </div>
                <span class="text-xs text-gray-400 shrink-0" x-text="(index + 1) + ' / ' + cards.length"></span>
            </div>

            {{-- Card --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">

                {{-- Word --}}
                <div class="px-8 py-8 text-center border-b border-gray-100">
                    <p class="text-3xl font-semibold text-gray-900" x-text="cards[index].word"></p>
                </div>

                {{-- Passive mode --}}
                <template x-if="mode === 'passive'">
                    <div class="px-8 py-6">

                        {{-- Before reveal --}}
                        <template x-if="!revealed">
                            <div class="text-center">
                                <button @click="revealed = true"
                                        class="bg-gray-100 text-gray-700 text-sm font-medium px-6 py-2.5 rounded-lg hover:bg-gray-200 transition-colors">
                                    Reveal
                                </button>
                            </div>
                        </template>

                        {{-- After reveal --}}
                        <template x-if="revealed">
                            <div>
                                <p class="text-center text-lg font-medium text-indigo-700 mb-2"
                                   x-text="cards[index].translation"></p>
                                <template x-if="cards[index].example">
                                    <p class="text-center text-sm text-gray-500 italic mb-6"
                                       x-text="cards[index].example"></p>
                                </template>
                                <template x-if="!cards[index].example">
                                    <div class="mb-6"></div>
                                </template>
                                <div class="flex gap-3 justify-center">
                                    <button @click="answer(false)"
                                            class="flex-1 max-w-36 border border-red-200 text-red-600 text-sm font-medium px-4 py-2.5 rounded-lg hover:bg-red-50 transition-colors">
                                        Missed it
                                    </button>
                                    <button @click="answer(true)"
                                            class="flex-1 max-w-36 border border-green-200 text-green-700 text-sm font-medium px-4 py-2.5 rounded-lg hover:bg-green-50 transition-colors">
                                        Got it
                                    </button>
                                </div>
                            </div>
                        </template>

                    </div>
                </template>

                {{-- Variants mode --}}
                <template x-if="mode === 'variants'">
                    <div class="px-8 py-6">
                        <div class="grid grid-cols-2 gap-3">
                            <template x-for="(option, i) in cards[index].options" :key="i">
                                <button
                                    @click="selectOption(option)"
                                    :disabled="answered"
                                    :class="{
                                        'border-gray-200 text-gray-700 hover:border-indigo-300 hover:bg-indigo-50': !answered,
                                        'border-green-300 bg-green-50 text-green-800': answered && option === cards[index].translation,
                                        'border-red-300 bg-red-50 text-red-700': answered && selected === option && option !== cards[index].translation,
                                        'border-gray-100 text-gray-400': answered && selected !== option && option !== cards[index].translation,
                                        'cursor-not-allowed': answered
                                    }"
                                    class="border rounded-lg px-4 py-3 text-sm font-medium text-left transition-colors"
                                    x-text="option">
                                </button>
                            </template>
                        </div>

                        <template x-if="answered">
                            <div class="mt-5 text-center">
                                <template x-if="cards[index].example">
                                    <p class="text-sm text-gray-500 italic mb-4" x-text="cards[index].example"></p>
                                </template>
                                <button @click="nextCard()"
                                        class="bg-gray-900 text-white text-sm font-medium px-6 py-2.5 rounded-lg hover:bg-gray-700 transition-colors">
                                    Next
                                </button>
                            </div>
                        </template>

                    </div>
                </template>

            </div>
        </div>
    </template>

</div>

<script>
function practice(cards) {
    return {
        mode: null,
        cards: cards,
        index: 0,
        revealed: false,
        answered: false,
        selected: null,
        correct: 0,
        incorrect: 0,
        done: false,

        startSession(mode) {
            this.mode = mode;
        },

        answer(isCorrect) {
            if (isCorrect) this.correct++; else this.incorrect++;
            this.nextCard();
        },

        selectOption(option) {
            if (this.answered) return;
            this.selected = option;
            this.answered = true;

            const isCorrect = option === this.cards[this.index].translation;
            if (isCorrect) this.correct++; else this.incorrect++;
        },

        nextCard() {
            if (this.index + 1 >= this.cards.length) {
                this.done = true;
                return;
            }
            this.index++;
            this.revealed = false;
            this.answered = false;
            this.selected = null;
        },
    };
}
</script>

@endsection
