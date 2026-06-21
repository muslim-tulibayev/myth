<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HabitController extends Controller
{
    public function index()
    {
        $habits = Habit::orderBy('sort_order')->get();

        // Daily quote: deterministic rotation by day-of-year
        $csvPath = storage_path('csv/100_best_quotes_habits_consistency.csv');
        $quotes  = array_slice(array_map(fn ($l) => str_getcsv($l, ',', '"', ''), file($csvPath)), 1); // skip header
        $idx     = (int) date('z') % count($quotes);
        $dailyQuote = ['text' => $quotes[$idx][0], 'author' => $quotes[$idx][1]];

        $totalHabits    = $habits->count();
        $completedToday = $habits->filter->isCompletedToday()->count();

        $weekCompleted = $habits->sum(fn ($h) => $h->completionsInWindow(7));
        $weekRate      = $totalHabits > 0
            ? round($weekCompleted / ($totalHabits * 7) * 100)
            : 0;

        $bestStreak = $habits->map->streak()->max() ?? 0;

        // Heatmap: 52 weeks ending at the current week
        $lastWeekStart = today()->startOfWeek(Carbon::MONDAY);
        $heatmapStart  = $lastWeekStart->copy()->subWeeks(51);

        $rawLogs = HabitLog::where('logged_date', '>=', $heatmapStart)
            ->where('logged_date', '<=', today())
            ->selectRaw('DATE(logged_date) as date, habit_id, SUM(amount) as total')
            ->groupBy('date', 'habit_id')
            ->get()
            ->groupBy('date');

        $heatmapCounts = [];
        foreach ($rawLogs as $date => $dayLogs) {
            $count = 0;
            foreach ($dayLogs as $log) {
                $habit = $habits->find($log->habit_id);
                if (! $habit) continue;
                if ($habit->isQuantified() ? $log->total >= $habit->target : $log->total > 0) {
                    $count++;
                }
            }
            $heatmapCounts[$date] = $count;
        }

        $heatmapWeeks  = [];
        $heatmapMonths = [];
        $current   = $heatmapStart->copy();
        $prevMonth = null;

        for ($w = 0; $w < 52; $w++) {
            $week = [];
            for ($d = 0; $d < 7; $d++) {
                $date   = $current->copy()->addDays($d);
                $week[] = [
                    'date'     => $date->toDateString(),
                    'count'    => $heatmapCounts[$date->toDateString()] ?? 0,
                    'isFuture' => $date->isAfter(today()),
                ];
            }
            $month = $current->format('M');
            if ($month !== $prevMonth) {
                $heatmapMonths[$w] = $month;
                $prevMonth = $month;
            }
            $heatmapWeeks[] = $week;
            $current->addWeek();
        }

        return view('dashboard', compact(
            'habits', 'completedToday', 'totalHabits', 'weekRate', 'bestStreak',
            'heatmapWeeks', 'heatmapMonths', 'dailyQuote'
        ));
    }

    public function show(Habit $habit)
    {
        $logs = $habit->logs()->orderByDesc('logged_date')->orderByDesc('id')->get();

        // Per-habit heatmap
        if ($habit->duration_days) {
            // Span exactly the duration window starting from created_at
            $habitStart   = $habit->created_at->startOfDay();
            $durationEnd  = $habitStart->copy()->addDays($habit->duration_days - 1);
            $heatmapStart = $habitStart->copy()->startOfWeek(Carbon::MONDAY);
            $weekEnd      = $durationEnd->copy()->endOfWeek(Carbon::SUNDAY);
            $numWeeks     = (int) $heatmapStart->diffInWeeks($weekEnd) + 1;
        } else {
            $durationEnd  = null;
            $heatmapStart = today()->startOfWeek(Carbon::MONDAY)->subWeeks(51);
            $numWeeks     = 52;
        }

        $dailyTotals = $habit->logs()
            ->where('logged_date', '>=', $heatmapStart)
            ->where('logged_date', '<=', today())
            ->selectRaw('DATE(logged_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $heatmapWeeks  = [];
        $heatmapMonths = [];
        $current   = $heatmapStart->copy();
        $prevMonth = null;

        for ($w = 0; $w < $numWeeks; $w++) {
            $week = [];
            for ($d = 0; $d < 7; $d++) {
                $date     = $current->copy()->addDays($d);
                $inactive = $date->isAfter(today())
                    || ($durationEnd && $date->isAfter($durationEnd));
                $week[]   = [
                    'date'     => $date->toDateString(),
                    'total'    => (float) ($dailyTotals[$date->toDateString()] ?? 0),
                    'isFuture' => $inactive,
                ];
            }
            $month = $current->format('M');
            if ($month !== $prevMonth) {
                $heatmapMonths[$w] = $month;
                $prevMonth = $month;
            }
            $heatmapWeeks[] = $week;
            $current->addWeek();
        }

        return view('habits.show', compact('habit', 'logs', 'heatmapWeeks', 'heatmapMonths'));
    }

    public function create()
    {
        return view('habits.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'goal'   => ['required', 'string'],
            'target' => ['nullable', 'numeric', 'min:0.01'],
            'unit'   => ['nullable', 'string', 'max:50'],
            'emoji'  => ['nullable', 'string', 'max:10'],
            'color'  => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $data['sort_order'] = Habit::max('sort_order') + 1;

        Habit::create($data);

        return redirect()->route('dashboard')->with('success', 'Habit created.');
    }

    public function edit(Habit $habit)
    {
        return view('habits.edit', compact('habit'));
    }

    public function update(Request $request, Habit $habit)
    {
        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'goal'   => ['required', 'string'],
            'target' => ['nullable', 'numeric', 'min:0.01'],
            'unit'   => ['nullable', 'string', 'max:50'],
            'emoji'  => ['nullable', 'string', 'max:10'],
            'color'  => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        if (empty($data['target'])) {
            $data['target'] = null;
            $data['unit']   = null;
        }

        $habit->update($data);

        return redirect()->route('habits.show', $habit)->with('success', 'Habit updated.');
    }

    public function destroy(Habit $habit)
    {
        $habit->delete();

        return redirect()->route('dashboard')->with('success', 'Habit archived.');
    }

    public function reorder(Request $request)
    {
        $request->validate(['ids' => ['required', 'array']]);

        foreach ($request->ids as $position => $id) {
            Habit::where('id', $id)->update(['sort_order' => $position]);
        }

        return response()->noContent();
    }
}
