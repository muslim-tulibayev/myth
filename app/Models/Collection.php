<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Collection extends Model
{
    protected $fillable = ['name', 'description', 'color', 'emoji'];

    private static array $palette = [
        '#6366f1', '#8b5cf6', '#ec4899', '#ef4444',
        '#f97316', '#eab308', '#22c55e', '#14b8a6',
        '#3b82f6', '#06b6d4',
    ];

    public static function randomColor(): string
    {
        return self::$palette[array_rand(self::$palette)];
    }

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }

    public function dueCount(): int
    {
        return $this->cards()->due()->count();
    }
}
