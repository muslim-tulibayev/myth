@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-semibold">{{ $habit->name }}</h1>
        <p class="text-sm text-gray-500 mt-1">Today's logs — {{ today()->format('F j, Y') }}</p>
    </div>
    <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">← Dashboard</a>
</div>

@if ($logs->isEmpty())
    <p class="text-gray-400 text-sm py-8 text-center">No logs yet today.</p>
@else
    <div class="space-y-2">
        @foreach ($logs as $log)
            <div class="bg-white border border-gray-200 rounded-lg px-4 py-3 flex items-center justify-between">
                <div>
                    <span class="text-sm font-medium">
                        @if ($habit->isQuantified())
                            +{{ number_format($log->amount, $log->amount == floor($log->amount) ? 0 : 2) }} {{ $habit->unit }}
                        @else
                            Done
                        @endif
                    </span>
                    <span class="text-xs text-gray-400 ml-2">{{ $log->created_at->format('H:i') }}</span>
                </div>
                <form method="POST" action="{{ route('habits.logs.destroy', [$habit, $log]) }}">
                    @csrf @method('DELETE')
                    <button type="submit"
                            onclick="return confirm('Remove this log?')"
                            class="text-xs text-red-500 hover:text-red-700 font-medium">
                        Remove
                    </button>
                </form>
            </div>
        @endforeach
    </div>

    @if ($habit->isQuantified())
        <p class="text-sm text-gray-500 mt-4">
            Total today:
            <strong>{{ number_format($logs->sum('amount'), 2) }} / {{ number_format($habit->target, $habit->target == floor($habit->target) ? 0 : 2) }} {{ $habit->unit }}</strong>
        </p>
    @endif
@endif

@endsection
