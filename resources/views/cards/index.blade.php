@extends('layouts.app')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Cards</h1>
</div>

@if ($cards->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 px-6 py-16 text-center text-gray-400">
        <p class="text-3xl mb-3">🃏</p>
        <p class="text-sm">No cards yet. Add cards from a collection.</p>
    </div>
@else
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 text-gray-400 text-xs uppercase tracking-wide">
                    <th class="text-left px-5 py-3 font-medium">Word</th>
                    <th class="text-left px-5 py-3 font-medium">Translation</th>
                    <th class="text-left px-5 py-3 font-medium">Collection</th>
                    <th class="text-left px-5 py-3 font-medium">Level</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($cards as $card)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3 font-medium text-gray-900">{{ $card->word }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $card->translation }}</td>
                        <td class="px-5 py-3">
                            <a href="{{ route('collections.show', $card->collection) }}"
                               class="inline-flex items-center gap-1.5 text-xs font-medium text-indigo-700 bg-indigo-50 px-2 py-1 rounded-md hover:bg-indigo-100 transition-colors">
                                {{ $card->collection->emoji ?? '📖' }} {{ $card->collection->name }}
                            </a>
                        </td>
                        <td class="px-5 py-3 text-gray-400 text-xs">{{ $card->levelLabel() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection
