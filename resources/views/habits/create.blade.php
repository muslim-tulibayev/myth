@extends('layouts.app')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-semibold">New habit</h1>
</div>

<form method="POST" action="{{ route('habits.store') }}" x-data="{ quantified: false }">
    @csrf

    <div class="space-y-5">

        <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Goal <span class="text-gray-400 font-normal">— why are you building this habit?</span></label>
            <textarea name="goal" rows="3" required
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">{{ old('goal') }}</textarea>
            @error('goal') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-3">
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" x-model="quantified" class="sr-only peer">
                <div class="w-10 h-6 bg-gray-200 peer-checked:bg-gray-900 rounded-full transition-colors"></div>
                <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-4"></div>
            </label>
            <span class="text-sm font-medium">Quantified habit</span>
            <span class="text-xs text-gray-400">(e.g. drink 8 glasses of water)</span>
        </div>

        <div x-show="quantified" x-cloak class="flex gap-3">
            <div class="flex-1">
                <label class="block text-sm font-medium mb-1">Target</label>
                <input type="number" name="target" value="{{ old('target') }}" min="0.01" step="any"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                @error('target') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium mb-1">Unit</label>
                <input type="text" name="unit" value="{{ old('unit') }}" placeholder="glasses, km, pages…"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                @error('unit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex gap-3">
            <div class="flex-1">
                <label class="block text-sm font-medium mb-1">Emoji</label>
                <input type="text" name="emoji" value="{{ old('emoji') }}" placeholder="🧘"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                @error('emoji') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Color</label>
                <input type="color" name="color" value="{{ old('color', '#6366f1') }}"
                       class="h-10 w-16 border border-gray-300 rounded-lg px-1 py-1 cursor-pointer">
                @error('color') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="bg-gray-900 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                Create habit
            </button>
            <a href="{{ route('dashboard') }}"
               class="text-sm font-medium text-gray-500 px-5 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                Cancel
            </a>
        </div>

    </div>
</form>

@endsection
