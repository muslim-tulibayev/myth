@extends('layouts.app')

@section('content')

{{-- Daily quote --}}
<div class="bg-white rounded-xl border border-gray-200 px-6 py-4 mb-4">
    <p class="text-sm italic text-gray-700 leading-relaxed">"{{ $dailyQuote['text'] }}"</p>
    <p class="text-xs text-gray-400 mt-2 text-right">— {{ $dailyQuote['author'] }}</p>
</div>

{{-- Goals for today --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="font-semibold text-gray-900">Goals for today</h2>
        <p class="text-xs text-gray-400 mt-0.5">{{ now()->format('l, F j') }}</p>
    </div>

    @if ($habits->isEmpty())
        <div class="px-6 py-10 text-center text-gray-400">
            <p class="text-3xl mb-3">🌱</p>
            <p class="text-sm">No habits yet.</p>
        </div>
    @else
        <div
            x-data
            x-init="
                Sortable.create($el, {
                    animation: 150,
                    handle: '[data-drag-handle]',
                    onEnd: function(evt) {
                        const ids = [...$el.querySelectorAll('[data-habit-id]')].map(el => el.dataset.habitId);
                        fetch('{{ route('habits.reorder') }}', {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                            },
                            body: JSON.stringify({ ids })
                        });
                    }
                })
            "
        >
            @foreach ($habits as $habit)
                @php
                    $completed = $habit->isCompletedToday();
                    $total     = $habit->todayTotal();
                    $streak    = $habit->streak();
                    $color     = $habit->color ?? '#6366f1';
                @endphp

                <div data-habit-id="{{ $habit->id }}"
                     class="flex items-center gap-3 px-6 py-4 border-b border-gray-50 last:border-b-0">

                    <button data-drag-handle type="button"
                            class="text-gray-300 hover:text-gray-400 cursor-grab active:cursor-grabbing shrink-0 text-lg leading-none">
                        ⠿
                    </button>

                    <span class="text-lg w-9 h-9 rounded-lg flex items-center justify-center shrink-0"
                          style="background-color: {{ $color }}">
                        {{ $habit->emoji ?? '✦' }}
                    </span>

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ $habit->name }}</p>
                        @if ($habit->isQuantified())
                            <p class="text-xs text-gray-400">
                                {{ number_format($total, $total == floor($total) ? 0 : 2) }}
                                / {{ number_format($habit->target, $habit->target == floor($habit->target) ? 0 : 2) }}
                                {{ $habit->unit }}
                            </p>
                        @endif
                    </div>

                    @if ($streak > 0)
                        <span class="text-xs font-medium text-orange-500 shrink-0">🔥 {{ $streak }}</span>
                    @endif

                    @if ($habit->isQuantified())
                        <form method="POST" action="{{ route('habits.logs.store', $habit) }}">
                            @csrf
                            <input type="hidden" name="amount" value="1">
                            <button type="submit"
                                    class="text-xs font-medium px-3 py-1.5 rounded-lg border transition-colors
                                           {{ $completed ? 'bg-green-50 border-green-300 text-green-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                                +1
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('habits.logs.store', $habit) }}">
                            @csrf
                            <button type="submit" @if($completed) disabled @endif
                                    class="text-xs font-medium px-3 py-1.5 rounded-lg border transition-colors
                                           {{ $completed ? 'bg-green-50 border-green-300 text-green-700 cursor-default' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                                {{ $completed ? '✓ Done' : 'Mark done' }}
                            </button>
                        </form>
                    @endif

                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Aggregate stats --}}
@if ($totalHabits > 0)
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 px-6 py-5">
            <p class="text-xs text-gray-400 mb-1">Today</p>
            <p class="text-2xl font-bold text-gray-900">
                {{ $completedToday }}<span class="text-base font-normal text-gray-400">/{{ $totalHabits }}</span>
            </p>
            <p class="text-xs text-gray-400 mt-1">habits completed</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-6 py-5">
            <p class="text-xs text-gray-400 mb-1">This week</p>
            <p class="text-2xl font-bold text-gray-900">
                {{ $weekRate }}<span class="text-base font-normal text-gray-400">%</span>
            </p>
            <p class="text-xs text-gray-400 mt-1">avg completion rate</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-6 py-5">
            <p class="text-xs text-gray-400 mb-1">Best active streak</p>
            <p class="text-2xl font-bold text-gray-900">🔥 {{ $bestStreak }}</p>
            <p class="text-xs text-gray-400 mt-1">days in a row</p>
        </div>
    </div>
@endif

{{-- Activity heatmap --}}
{{-- cell=10px, gap=2px → step=12px per week; day-label col=28px + 2px gap = 30px left offset --}}
{{-- total width: 52×10 + 51×2 + 30 = 652px ≤ 656px available (fits without scroll) --}}
@if ($totalHabits > 0)
<div class="bg-white rounded-xl border border-gray-200 px-6 py-5 mt-4"
     x-data="{ tip: null, tipX: 0, tipY: 0 }"
     @mouseover="const d = $event.target.dataset; if (d.date) { tip = d.date + (parseInt(d.count) > 0 ? ' · ' + d.count + '/{{ $totalHabits }} habits' : ' · No activity'); tipX = $event.clientX; tipY = $event.clientY }"
     @mouseleave="tip = null">

    <h3 class="text-sm font-semibold text-gray-900 mb-4">Activity</h3>

    {{-- Month labels: absolutely positioned so they never collide --}}
    <div style="position: relative; height: 14px; margin-left: 30px; margin-bottom: 2px">
        @foreach ($heatmapMonths as $weekIdx => $monthLabel)
            <span style="position: absolute; bottom: 0; left: {{ $weekIdx * 12 }}px;
                         font-size: 10px; line-height: 1; color: #9ca3af; white-space: nowrap">
                {{ $monthLabel }}
            </span>
        @endforeach
    </div>

    {{-- Grid: day labels + week columns --}}
    <div style="display: flex; gap: 2px">

        {{-- Day-of-week labels (Mon / Wed / Fri / Sun) --}}
        <div style="display: flex; flex-direction: column; gap: 2px; width: 28px; flex-shrink: 0">
            @foreach (['Mon', '', 'Wed', '', 'Fri', '', 'Sun'] as $label)
                <div style="height: 10px; display: flex; align-items: center">
                    @if ($label)
                        <span style="font-size: 10px; line-height: 1; color: #9ca3af">{{ $label }}</span>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- 52 week columns --}}
        @foreach ($heatmapWeeks as $week)
            <div style="display: flex; flex-direction: column; gap: 2px">
                @foreach ($week as $day)
                    @php
                        $bg = match(true) {
                            $day['isFuture'] || $day['count'] === 0 => '#ebedf0',
                            $day['count'] === 1                     => '#9be9a8',
                            $day['count'] === 2                     => '#40c463',
                            default                                 => '#216e39',
                        };
                    @endphp
                    <div data-date="{{ $day['date'] }}"
                         data-count="{{ $day['count'] }}"
                         style="width: 10px; height: 10px; border-radius: 2px; background-color: {{ $bg }}; cursor: default"></div>
                @endforeach
            </div>
        @endforeach

    </div>

    {{-- Legend --}}
    <div style="display: flex; align-items: center; gap: 6px; margin-top: 10px; justify-content: flex-end">
        <span style="font-size: 10px; color: #9ca3af">Less</span>
        <div style="width: 10px; height: 10px; border-radius: 2px; background-color: #ebedf0"></div>
        <div style="width: 10px; height: 10px; border-radius: 2px; background-color: #9be9a8"></div>
        <div style="width: 10px; height: 10px; border-radius: 2px; background-color: #40c463"></div>
        <div style="width: 10px; height: 10px; border-radius: 2px; background-color: #216e39"></div>
        <span style="font-size: 10px; color: #9ca3af">More</span>
    </div>

    {{-- Floating tooltip --}}
    <div x-show="tip" x-text="tip" x-cloak
         :style="{
             position: 'fixed',
             left: (tipX + 12) + 'px',
             top: (tipY - 36) + 'px',
             background: '#1f2937',
             color: '#fff',
             fontSize: '11px',
             padding: '4px 8px',
             borderRadius: '4px',
             pointerEvents: 'none',
             zIndex: '50',
             whiteSpace: 'nowrap',
             boxShadow: '0 1px 4px rgba(0,0,0,.3)'
         }">
    </div>
</div>
@endif

@endsection
