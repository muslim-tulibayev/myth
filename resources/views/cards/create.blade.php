@extends('layouts.app')

@section('content')

<div class="mb-6">
    <a href="{{ route('collections.show', $collection) }}"
       class="text-sm text-gray-500 hover:text-gray-700 transition-colors">← {{ $collection->name }}</a>
    <h1 class="text-2xl font-semibold mt-1">Add card</h1>
</div>

<form method="POST" action="{{ route('collections.cards.store', $collection) }}">
    @csrf

    <div class="space-y-5">

        <div class="flex gap-3">
            <div class="flex-1">
                <label class="block text-sm font-medium mb-1">Word</label>
                <input type="text" name="word" value="{{ old('word') }}" required autofocus
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                @error('word') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium mb-1">Translation</label>
                <input type="text" name="translation" value="{{ old('translation') }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                @error('translation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Example sentence <span class="text-gray-400 font-normal">— optional</span></label>
            <textarea name="example" rows="2"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">{{ old('example') }}</textarea>
            @error('example') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Notes <span class="text-gray-400 font-normal">— optional</span></label>
            <textarea name="notes" rows="2"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">{{ old('notes') }}</textarea>
            @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="bg-gray-900 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                Add card
            </button>
            <a href="{{ route('collections.show', $collection) }}"
               class="text-sm font-medium text-gray-500 px-5 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                Cancel
            </a>
        </div>

    </div>
</form>

@endsection
