@extends('layouts.app')

@section('content')

{{-- Header --}}
<div class="flex items-start justify-between mb-6">
    <div class="flex items-center gap-3">
        <span class="w-12 h-12 rounded-xl flex items-center justify-center text-xl shrink-0"
              style="background-color: {{ $collection->color }}">
            {{ $collection->emoji ?? '📖' }}
        </span>
        <div>
            <h1 class="text-2xl font-semibold">{{ $collection->name }}</h1>
            @if ($collection->description)
                <p class="text-sm text-gray-500 mt-0.5">{{ $collection->description }}</p>
            @endif
        </div>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        <a href="{{ route('collections.edit', $collection) }}"
           class="text-sm text-gray-500 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">
            Edit
        </a>
        @if ($cards->isNotEmpty())
            <a href="{{ route('collections.practice.show', $collection) }}"
               class="text-sm text-gray-700 font-medium px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                Practice all
            </a>
        @endif
        @if ($dueCount > 0)
            <a href="{{ route('collections.review.show', $collection) }}"
               class="bg-indigo-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-indigo-500 transition-colors">
                Review {{ $dueCount }} due
            </a>
        @else
            <button disabled
                    class="bg-gray-100 text-gray-400 text-sm font-medium px-4 py-2 rounded-lg cursor-not-allowed">
                No cards due
            </button>
        @endif
    </div>
</div>

{{-- Cards list --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">

    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-900">Cards <span class="text-gray-400 font-normal">({{ $cards->count() }})</span></h2>
        <a href="{{ route('collections.cards.create', $collection) }}"
           class="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors">
            + Add card
        </a>
    </div>

    @if ($cards->isEmpty())
        <div class="px-6 py-12 text-center text-sm text-gray-400">
            No cards yet. Add your first word to get started.
        </div>
    @else
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">Word</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">Translation</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide w-28">Status</th>
                    <th class="px-6 py-3 w-16"></th>
                </tr>
            </thead>
            @php
                $levelColors = [
                    0 => 'bg-gray-100 text-gray-600',
                    1 => 'bg-blue-100 text-blue-700',
                    2 => 'bg-yellow-100 text-yellow-700',
                    3 => 'bg-orange-100 text-orange-700',
                    4 => 'bg-green-100 text-green-700',
                ];
                $shortDiff = fn ($dt) => str_replace(
                    ['minutes', 'minute', 'seconds', 'second', 'hours', 'hour', 'weeks', 'week', 'months', 'month', 'years', 'year'],
                    ['mins',    'min',    'secs',    'sec',    'hrs',   'hr',   'wks',   'wk',   'mos',    'mo',    'yrs',   'yr'],
                    $dt->diffForHumans()
                );
            @endphp
            @foreach ($cards as $card)
                <tbody
                    x-data="{ open: false, top: 0, left: 0 }"
                    @mouseenter="open = true; let r = $el.getBoundingClientRect(); top = r.top; left = r.right + 12"
                    @mouseleave="open = false"
                >
                    <tr class="hover:bg-gray-50 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <td class="px-6 py-3 font-medium text-gray-900">{{ $card->word }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $card->translation }}</td>
                        <td class="px-6 py-3">
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $levelColors[$card->level] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $card->levelLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('collections.cards.edit', [$collection, $card]) }}"
                               class="text-xs text-gray-400 hover:text-gray-600 transition-colors">
                                Edit
                            </a>
                        </td>
                    </tr>
                    <template x-teleport="body">
                        <div
                            x-show="open"
                            x-cloak
                            :style="`position: fixed; top: ${top}px; left: ${left}px; z-index: 50;`"
                            class="w-72 bg-white rounded-xl border border-gray-200 shadow-lg p-4 pointer-events-none"
                        >
                            <div class="flex items-center gap-2 mb-2">
                                <p class="font-semibold text-gray-900">{{ $card->word }}</p>
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $levelColors[$card->level] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $card->levelLabel() }}
                                </span>
                            </div>
                            <p class="text-sm font-medium text-indigo-700 mb-3">{{ $card->translation }}</p>
                            @if ($card->example)
                                <p class="text-sm text-gray-500 italic mb-2">{{ $card->example }}</p>
                            @else
                                <p class="text-sm text-gray-400 italic mb-2">No example provided</p>
                            @endif
                            @if ($card->notes)
                                <p class="text-xs text-gray-400"><span class="font-medium not-italic">Notes:</span> {{ $card->notes }}</p>
                            @else
                                <p class="text-xs text-gray-400">No notes</p>
                            @endif
                            <hr class="my-3 border-gray-100">
                            <div class="space-y-1.5">
                                <div class="flex justify-between items-baseline gap-4">
                                    <span class="text-xs text-gray-400 shrink-0">Last reviewed</span>
                                    @if ($card->reviewed_at)
                                        <span class="text-xs text-gray-500 text-right">{{ $card->reviewed_at->format('M j, Y') }} · {{ $shortDiff($card->reviewed_at) }}</span>
                                    @else
                                        <span class="text-xs text-gray-400">Never</span>
                                    @endif
                                </div>
                                <div class="flex justify-between items-baseline gap-4">
                                    <span class="text-xs text-gray-400 shrink-0">Added</span>
                                    <span class="text-xs text-gray-500 text-right">{{ $card->created_at->format('M j, Y') }} · {{ $shortDiff($card->created_at) }}</span>
                                </div>
                            </div>
                        </div>
                    </template>
                </tbody>
            @endforeach
        </table>
    @endif
</div>

@endsection
