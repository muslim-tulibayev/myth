@extends('layouts.app')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Collections</h1>
    <a href="{{ route('collections.create') }}"
       class="bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
        New collection
    </a>
</div>

@if ($collections->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 px-6 py-16 text-center text-gray-400">
        <p class="text-3xl mb-3">📚</p>
        <p class="text-sm">No collections yet. Create one to start learning vocabulary.</p>
    </div>
@else
    <div class="space-y-3">
        @foreach ($collections as $collection)
            <div class="flex items-center gap-4 bg-white rounded-xl border border-gray-200 px-5 py-4 hover:border-gray-300 transition-colors">

                <a href="{{ route('collections.show', $collection) }}" class="flex items-center gap-4 flex-1 min-w-0">
                    <span class="w-10 h-10 rounded-lg flex items-center justify-center text-lg shrink-0"
                          style="background-color: {{ $collection->color }}">
                        {{ $collection->emoji ?? '📖' }}
                    </span>

                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 truncate">{{ $collection->name }}</p>
                        @if ($collection->description)
                            <p class="text-xs text-gray-400 truncate mt-0.5">{{ $collection->description }}</p>
                        @endif
                    </div>
                </a>

                <div class="flex items-center gap-3 shrink-0 text-sm text-gray-500">
                    <span>{{ $collection->cards_count }} {{ Str::plural('card', $collection->cards_count) }}</span>
                    @if ($collection->due_count > 0)
                        <a href="{{ route('collections.review.show', $collection) }}"
                           class="bg-indigo-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-indigo-500 transition-colors">
                            Review {{ $collection->due_count }}
                        </a>
                    @endif
                </div>

            </div>
        @endforeach
    </div>
@endif

@endsection
