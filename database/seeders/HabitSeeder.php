<?php

namespace Database\Seeders;

use App\Models\Habit;
use Illuminate\Database\Seeder;

class HabitSeeder extends Seeder
{
    public function run(): void
    {
        $habits = [
            [
                'name'          => 'Meditate',
                'goal'          => 'Build focus and reduce anxiety through daily meditation',
                'target'        => null,
                'unit'          => null,
                'emoji'         => '🧘',
                'color'         => '#8b5cf6',
                'sort_order'    => 0,
                'duration_days' => null,
            ],
            [
                'name'          => 'Drink Water',
                'goal'          => 'Stay hydrated to maintain energy throughout the day',
                'target'        => 8,
                'unit'          => 'glasses',
                'emoji'         => '💧',
                'color'         => '#3b82f6',
                'sort_order'    => 1,
                'duration_days' => null,
            ],
            [
                'name'          => 'Learning DevOps',
                'goal'          => 'Study DevOps tools and concepts daily to build proficiency over 90 days',
                'target'        => 60,
                'unit'          => 'mins',
                'emoji'         => '💻',
                'color'         => '#06b6d4',
                'sort_order'    => 2,
                'duration_days' => 90,
            ],
        ];

        foreach ($habits as $data) {
            Habit::updateOrCreate(['name' => $data['name']], $data);
        }
    }
}
