@extends('layouts.app')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-semibold">Edit collection</h1>
</div>

<form method="POST" action="{{ route('collections.update', $collection) }}">
    @csrf
    @method('PATCH')

    <div class="space-y-5">

        <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input type="text" name="name" value="{{ old('name', $collection->name) }}" required autofocus
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Description <span class="text-gray-400 font-normal">— optional</span></label>
            <textarea name="description" rows="2"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">{{ old('description', $collection->description) }}</textarea>
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-3">
            <div class="flex-1">
                <label class="block text-sm font-medium mb-1">Emoji <span class="text-gray-400 font-normal">— optional</span></label>
                <input type="text" name="emoji" value="{{ old('emoji', $collection->emoji) }}" placeholder="📖"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                @error('emoji') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Color</label>
                <input type="color" name="color" value="{{ old('color', $collection->color) }}"
                       class="h-10 w-16 border border-gray-300 rounded-lg px-1 py-1 cursor-pointer">
                @error('color') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="bg-gray-900 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                Save changes
            </button>
            <a href="{{ route('collections.show', $collection) }}"
               class="text-sm font-medium text-gray-500 px-5 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                Cancel
            </a>
        </div>

    </div>
</form>

@endsection
