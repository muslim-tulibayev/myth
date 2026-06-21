<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Card extends Model
{
    protected $fillable = ['collection_id', 'word', 'translation', 'example', 'notes', 'level', 'reviewed_at'];

    protected $casts = [
        'level'       => 'integer',
        'reviewed_at' => 'datetime',
    ];

    private const INTERVALS = [0 => 1, 1 => 3, 2 => 7, 3 => 30];

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function scopeDue(Builder $query): Builder
    {
        return $query->where('level', '<', 4)->where(function ($q) {
            $q->whereNull('reviewed_at');
            foreach (self::INTERVALS as $level => $days) {
                $q->orWhere(function ($inner) use ($level, $days) {
                    $inner->where('level', $level)
                          ->whereDate('reviewed_at', '<=', today()->subDays($days));
                });
            }
        });
    }

    public function isDue(): bool
    {
        if ($this->level >= 4) return false;
        if (is_null($this->reviewed_at)) return true;

        $days = self::INTERVALS[$this->level];
        return $this->reviewed_at->copy()->startOfDay()->addDays($days)->lte(today());
    }

    public function levelLabel(): string
    {
        return match ($this->level) {
            0       => 'New',
            1       => 'Level 1',
            2       => 'Level 2',
            3       => 'Level 3',
            default => 'Mastered',
        };
    }

    public function recordCorrect(): void
    {
        $this->level       = min($this->level + 1, 4);
        $this->reviewed_at = now();
        $this->save();
    }

    public function recordWrong(): void
    {
        $this->level       = 0;
        $this->reviewed_at = now();
        $this->save();
    }
}
