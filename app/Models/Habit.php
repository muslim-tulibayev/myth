<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Habit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'goal',
        'target',
        'unit',
        'emoji',
        'color',
        'sort_order',
        'duration_days',
    ];

    protected $casts = [
        'target' => 'decimal:2',
        'sort_order' => 'integer',
        'duration_days' => 'integer',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(HabitLog::class);
    }

    public function isQuantified(): bool
    {
        return $this->target !== null;
    }

    public function streak(): int
    {
        $dailyTotals = $this->logs()
            ->selectRaw('DATE(logged_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderByDesc('date')
            ->pluck('total', 'date');

        if ($dailyTotals->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $checkDate = today();

        while (true) {
            $total = $dailyTotals->get($checkDate->toDateString(), 0);

            $completed = $this->isQuantified()
                ? $total >= $this->target
                : $total > 0;

            if (! $completed) {
                break;
            }

            $streak++;
            $checkDate->subDay();
        }

        return $streak;
    }

    public function bestStreak(): int
    {
        $dailyTotals = $this->logs()
            ->selectRaw('DATE(logged_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $best = 0;
        $current = 0;
        $prevDate = null;

        foreach ($dailyTotals as $date => $total) {
            $completed = $this->isQuantified()
                ? $total >= $this->target
                : $total > 0;

            if (! $completed) {
                $current = 0;
                $prevDate = null;
                continue;
            }

            $expectedPrev = \Carbon\Carbon::parse($date)->subDay()->toDateString();
            $current = ($prevDate === $expectedPrev) ? $current + 1 : 1;
            $best = max($best, $current);
            $prevDate = $date;
        }

        return $best;
    }

    public function completionsInWindow(int $days): int
    {
        $from = today()->subDays($days - 1)->toDateString();
        $to   = today()->toDateString();

        return $this->logs()
            ->selectRaw('DATE(logged_date) as date, SUM(amount) as total')
            ->whereRaw('DATE(logged_date) BETWEEN ? AND ?', [$from, $to])
            ->groupBy('date')
            ->get()
            ->filter(fn($row) => $this->isQuantified()
                ? $row->total >= $this->target
                : $row->total > 0)
            ->count();
    }

    public function totalCompletions(): int
    {
        return $this->logs()
            ->selectRaw('DATE(logged_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->get()
            ->filter(fn($row) => $this->isQuantified()
                ? $row->total >= $this->target
                : $row->total > 0)
            ->count();
    }

    public function isCompletedToday(): bool
    {
        $total = $this->todayTotal();

        return $this->isQuantified()
            ? $total >= $this->target
            : $total > 0;
    }

    public function todayTotal(): float
    {
        return (float) $this->logs()
            ->whereDate('logged_date', today())
            ->sum('amount');
    }
}
