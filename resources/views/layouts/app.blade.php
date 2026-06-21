<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MYTH</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-900">

<div class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    <aside class="w-52 bg-white border-r border-gray-200 flex flex-col shrink-0">
        <div class="px-5 py-5 border-b border-gray-100">
            <span class="text-base font-bold tracking-tight">MYTH</span>
        </div>
        <nav class="flex-1 p-2 space-y-0.5 overflow-y-auto">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('dashboard') ? 'bg-green-100 text-green-800' : 'text-gray-600 hover:bg-gray-100' }}">
                Dashboard
            </a>
            <div class="mt-2 mx-1 rounded-xl bg-green-50 p-1">
                <div class="pb-0.5 px-2 pt-1.5">
                    <span class="text-xs font-medium text-green-500 uppercase tracking-wide">Habits</span>
                </div>
                @foreach ($navHabits as $navHabit)
                    <a href="{{ route('habits.show', $navHabit) }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors
                              {{ request()->is('habits/' . $navHabit->id) ? 'bg-green-100 text-green-800 font-medium' : 'text-green-900/60 hover:bg-green-100' }}">
                        <span>{{ $navHabit->emoji ?? '✦' }}</span>
                        <span class="truncate">{{ $navHabit->name }}</span>
                    </a>
                @endforeach
            </div>

            <div class="mt-2 mx-1 rounded-xl bg-indigo-50 p-1">
                <div class="pb-0.5 px-2 pt-1.5">
                    <span class="text-xs font-medium text-indigo-400 uppercase tracking-wide">Vocabulary</span>
                </div>
                <a href="{{ route('collections.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('collections.*') ? 'bg-indigo-100 text-indigo-800' : 'text-indigo-900/60 hover:bg-indigo-100' }}">
                    Collections
                </a>
                <a href="{{ route('cards.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('cards.*') ? 'bg-indigo-100 text-indigo-800' : 'text-indigo-900/60 hover:bg-indigo-100' }}">
                    Cards
                </a>
            </div>
        </nav>
    </aside>

    {{-- Main content --}}
    <main class="flex-1 overflow-y-auto">
        <div class="max-w-3xl mx-auto px-8 py-8">

            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-cloak
                     x-init="setTimeout(() => show = false, 3000)"
                     class="mb-6 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('info'))
                <div x-data="{ show: true }" x-show="show" x-cloak
                     x-init="setTimeout(() => show = false, 4000)"
                     class="mb-6 text-sm text-blue-700 bg-blue-50 border border-blue-200 rounded-lg px-4 py-3">
                    {{ session('info') }}
                </div>
            @endif

            @yield('content')

        </div>
    </main>

</div>

</body>
</html>
