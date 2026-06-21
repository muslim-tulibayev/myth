<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitLog;
use Illuminate\Http\Request;

class HabitLogController extends Controller
{
    public function index(Habit $habit)
    {
        $logs = $habit->logs()
            ->whereDate('logged_date', today())
            ->latest()
            ->get();

        return view('habits.logs', compact('habit', 'logs'));
    }

    public function store(Request $request, Habit $habit)
    {
        $data = $request->validate([
            'amount' => ['nullable', 'numeric', 'min:0.01'],
            'note'   => ['nullable', 'string', 'max:500'],
        ]);

        $habit->logs()->create([
            'amount'      => $data['amount'] ?? 1,
            'note'        => $data['note'] ?? null,
            'logged_date' => today(),
        ]);

        return back()->with('success', 'Logged.');
    }

    public function destroy(Habit $habit, HabitLog $log)
    {
        abort_if($log->habit_id !== $habit->id, 404);

        $log->delete();

        return back()->with('success', 'Log removed.');
    }
}
