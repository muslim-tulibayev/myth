<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\Collection;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class VocabularySeeder extends Seeder
{
    public function run(): void
    {
        // Collection 1: Spanish basics
        $spanish = Collection::create([
            'name' => 'Spanish Basics',
            'description' => 'Everyday Spanish vocabulary',
            'color' => '#ef4444',
            'emoji' => '🇪🇸',
        ]);

        $spanishCards = [
            ['word' => 'hola', 'translation' => 'hello', 'example' => '¡Hola! ¿Cómo estás?'],
            ['word' => 'gracias', 'translation' => 'thank you', 'example' => 'Muchas gracias por tu ayuda.'],
            ['word' => 'por favor', 'translation' => 'please', 'example' => 'Un café, por favor.'],
            ['word' => 'bueno', 'translation' => 'good', 'example' => 'Esto es muy bueno.'],
            ['word' => 'malo', 'translation' => 'bad', 'example' => 'El tiempo está malo hoy.'],
            ['word' => 'grande', 'translation' => 'big / large', 'example' => 'Vivo en una ciudad grande.'],
            ['word' => 'pequeño', 'translation' => 'small / little', 'example' => 'Mi apartamento es pequeño.'],
            ['word' => 'agua', 'translation' => 'water', 'example' => '¿Puedo tener un vaso de agua?'],
            ['word' => 'casa', 'translation' => 'house / home', 'example' => 'Estoy en casa.'],
            ['word' => 'trabajo', 'translation' => 'work / job', 'example' => 'Voy al trabajo a las ocho.'],
        ];

        foreach ($spanishCards as $i => $data) {
            $level = $i % 5; // spread across levels 0-4
            Card::create([
                ...$data,
                'collection_id' => $spanish->id,
                'level' => $level,
                'reviewed_at' => $level === 0 ? null : $this->reviewedAtForLevel($level),
            ]);
        }

        // Collection 2: Programming terms
        $programming = Collection::create([
            'name' => 'Programming Terms',
            'description' => 'CS & software engineering vocabulary',
            'color' => '#6366f1',
            'emoji' => '💻',
        ]);

        $programmingCards = [
            ['word' => 'idempotent', 'translation' => 'producing the same result no matter how many times applied', 'example' => 'DELETE requests should be idempotent.'],
            ['word' => 'polymorphism', 'translation' => 'the ability of different objects to respond to the same interface', 'example' => 'Polymorphism allows a Dog and a Cat to both implement speak().'],
            ['word' => 'immutable', 'translation' => 'cannot be changed after creation', 'example' => 'Strings in Python are immutable.'],
            ['word' => 'race condition', 'translation' => 'a bug caused by two operations depending on shared state with unpredictable ordering', 'notes' => 'Common in concurrent or multi-threaded code'],
            ['word' => 'debounce', 'translation' => 'delay execution until input stops firing for a set duration', 'example' => 'Debounce the search input to avoid flooding the API.'],
            ['word' => 'sharding', 'translation' => 'splitting a database horizontally across multiple servers', 'notes' => 'Each shard holds a subset of the rows'],
            ['word' => 'memoization', 'translation' => 'caching the result of a function based on its inputs', 'example' => 'Memoize expensive recursive calculations.'],
        ];

        foreach ($programmingCards as $i => $data) {
            $level = ($i * 2) % 5;
            Card::create([
                ...$data,
                'collection_id' => $programming->id,
                'level' => $level,
                'reviewed_at' => $level === 0 ? null : $this->reviewedAtForLevel($level),
            ]);
        }

        // Collection 3: New/empty — all cards due
        $japanese = Collection::create([
            'name' => 'Japanese N5',
            'description' => 'JLPT N5 core vocabulary',
            'color' => '#ec4899',
            'emoji' => '🗾',
        ]);

        $japaneseCards = [
            ['word' => 'ありがとう (arigatou)', 'translation' => 'thank you'],
            ['word' => 'すみません (sumimasen)', 'translation' => 'excuse me / sorry'],
            ['word' => 'はい (hai)', 'translation' => 'yes'],
            ['word' => 'いいえ (iie)', 'translation' => 'no'],
            ['word' => '水 (mizu)', 'translation' => 'water'],
            ['word' => '食べる (taberu)', 'translation' => 'to eat'],
            ['word' => '飲む (nomu)', 'translation' => 'to drink'],
            ['word' => '大きい (ookii)', 'translation' => 'big'],
            ['word' => '小さい (chiisai)', 'translation' => 'small'],
        ];

        foreach ($japaneseCards as $data) {
            Card::create([
                ...$data,
                'collection_id' => $japanese->id,
                'level' => 0,
                'reviewed_at' => null, // all new = all due
            ]);
        }
    }

    private function reviewedAtForLevel(int $level): Carbon
    {
        // Make cards due NOW for easy testing: set reviewed_at far enough in the past
        $intervals = [1 => 3, 2 => 7, 3 => 30, 4 => 999];
        $days = $intervals[$level] ?? 1;
        return now()->subDays($days + 1);
    }
}
