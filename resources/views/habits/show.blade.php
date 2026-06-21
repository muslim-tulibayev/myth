@extends('layouts.app')

@section('content')

@php
    $completed = $habit->isCompletedToday();
    $total     = $habit->todayTotal();
    $streak    = $habit->streak();
    $color     = $habit->color ?? '#6366f1';
@endphp

{{-- Goals for today by [habit] --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-6">

    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
        <span class="text-lg w-10 h-10 rounded-lg flex items-center justify-center shrink-0"
              style="background-color: {{ $color }}">
            {{ $habit->emoji ?? '✦' }}
        </span>
        <div class="flex-1 min-w-0">
            <h2 class="font-semibold text-gray-900">{{ $habit->name }}</h2>
            @if ($habit->duration_days)
                @php
                    $dayNumber = min(today()->diffInDays($habit->created_at->startOfDay()) + 1, $habit->duration_days);
                    $progressPct = round($dayNumber / $habit->duration_days * 100);
                @endphp
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-xs text-gray-400">Day {{ $dayNumber }} of {{ $habit->duration_days }}</span>
                    <div class="flex-1 h-1 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-green-400 rounded-full" style="width: {{ $progressPct }}%"></div>
                    </div>
                </div>
            @else
                <p class="text-xs text-gray-400">Goals for today</p>
            @endif
        </div>
    </div>

    {{-- Goal text --}}
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
        <p class="text-sm text-gray-600 italic">"{{ $habit->goal }}"</p>
    </div>

    {{-- Today's progress + log form --}}
    <div class="px-6 py-4">
        <div class="flex items-center gap-4 flex-wrap">

            <div class="flex-1 min-w-0">
                @if ($habit->isQuantified())
                    <p class="text-sm font-medium text-gray-800">
                        {{ number_format($total, $total == floor($total) ? 0 : 2) }}
                        / {{ number_format($habit->target, $habit->target == floor($habit->target) ? 0 : 2) }}
                        {{ $habit->unit }} today
                    </p>
                @else
                    <p class="text-sm font-medium text-gray-800">
                        {{ $completed ? 'Done for today ✓' : 'Not done yet' }}
                    </p>
                @endif
                @if ($streak > 0)
                    <p class="text-xs text-orange-500 mt-0.5">🔥 {{ $streak }} day streak</p>
                @endif
            </div>

            <form method="POST" action="{{ route('habits.logs.store', $habit) }}"
                  class="flex items-center gap-2 flex-wrap">
                @csrf
                @if ($habit->isQuantified())
                    <input type="number" name="amount" value="1" min="0.01" step="any"
                           class="w-20 text-sm border border-gray-300 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-green-300">
                @endif
                <input type="text" name="note" placeholder="Note (optional)"
                       class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-green-300 w-44">
                <button type="submit" @if(!$habit->isQuantified() && $completed) disabled @endif
                        class="text-sm font-medium px-4 py-1.5 rounded-lg border transition-colors whitespace-nowrap
                               {{ !$habit->isQuantified() && $completed
                                   ? 'bg-green-50 border-green-300 text-green-700 cursor-default'
                                   : 'bg-gray-900 border-gray-900 text-white hover:bg-gray-700' }}">
                    @if (!$habit->isQuantified() && $completed)
                        ✓ Done
                    @elseif ($habit->isQuantified())
                        Log
                    @else
                        Mark done
                    @endif
                </button>
            </form>

        </div>
    </div>

</div>

{{-- Activity heatmap --}}
{{-- cell=10px, gap=2px → step=12px; day-label col=28px + 2px gap = 30px left offset --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-6"
     x-data="{ tip: null, tipX: 0, tipY: 0 }"
     @mouseover="const d = $event.target.dataset; if (d.date) {
         const t = parseFloat(d.total);
         @if ($habit->isQuantified())
             const target = {{ (float) $habit->target }};
             tip = d.date + (t > 0 ? ' · ' + t + '/{{ $habit->target }} {{ $habit->unit }}' : ' · No activity');
         @else
             tip = d.date + (t > 0 ? ' · Done' : ' · No activity');
         @endif
         tipX = $event.clientX; tipY = $event.clientY;
     }"
     @mouseleave="tip = null">

    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900">Activity</h3>
    </div>

    <div class="px-6 py-4">
        {{-- Month labels --}}
        <div style="position: relative; height: 14px; margin-left: 30px; margin-bottom: 2px">
            @foreach ($heatmapMonths as $weekIdx => $monthLabel)
                <span style="position: absolute; bottom: 0; left: {{ $weekIdx * 12 }}px;
                             font-size: 10px; line-height: 1; color: #9ca3af; white-space: nowrap">
                    {{ $monthLabel }}
                </span>
            @endforeach
        </div>

        {{-- Grid --}}
        <div style="display: flex; gap: 2px">

            {{-- Day labels --}}
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
                            if ($day['isFuture'] || $day['total'] == 0) {
                                $bg = '#ebedf0';
                            } elseif (! $habit->isQuantified()) {
                                $bg = '#216e39';
                            } else {
                                $t = $day['total'];
                                $tgt = (float) $habit->target;
                                $bg = $t >= $tgt ? '#216e39' : ($t >= $tgt / 2 ? '#40c463' : '#9be9a8');
                            }
                        @endphp
                        <div data-date="{{ $day['date'] }}"
                             data-total="{{ $day['total'] }}"
                             style="width: 10px; height: 10px; border-radius: 2px; background-color: {{ $bg }}; cursor: default"></div>
                    @endforeach
                </div>
            @endforeach

        </div>

        {{-- Legend --}}
        <div style="display: flex; align-items: center; gap: 6px; margin-top: 10px; justify-content: flex-end">
            <span style="font-size: 10px; color: #9ca3af">Less</span>
            <div style="width: 10px; height: 10px; border-radius: 2px; background-color: #ebedf0"></div>
            @if ($habit->isQuantified())
                <div style="width: 10px; height: 10px; border-radius: 2px; background-color: #9be9a8"></div>
                <div style="width: 10px; height: 10px; border-radius: 2px; background-color: #40c463"></div>
            @endif
            <div style="width: 10px; height: 10px; border-radius: 2px; background-color: #216e39"></div>
            <span style="font-size: 10px; color: #9ca3af">More</span>
        </div>
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

{{-- Log history --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900">History</h3>
    </div>

    @if ($logs->isEmpty())
        <div class="px-6 py-10 text-center text-sm text-gray-400">No logs yet.</div>
    @else
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide w-36">Date</th>
                    @if ($habit->isQuantified())
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide w-28">
                            {{ ucfirst($habit->unit ?? 'Amount') }}
                        </th>
                    @endif
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wide">Note</th>
                    <th class="px-6 py-3 w-20"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                    <tr class="border-b border-gray-50 last:border-b-0 hover:bg-gray-50">
                        <td class="px-6 py-3 text-gray-700">
                            {{ $log->logged_date->format('M j, Y') }}
                        </td>
                        @if ($habit->isQuantified())
                            <td class="px-6 py-3 text-gray-700">
                                {{ number_format($log->amount, $log->amount == floor($log->amount) ? 0 : 2) }}
                            </td>
                        @endif
                        <td class="px-6 py-3 text-gray-500">{{ $log->note ?? '—' }}</td>
                        <td class="px-6 py-3 text-right">
                            @if ($log->logged_date->isToday())
                                <form method="POST"
                                      action="{{ route('habits.logs.destroy', [$habit, $log]) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Remove this log?')"
                                            class="text-xs text-red-400 hover:text-red-600 transition-colors">
                                        Remove
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@endsection
