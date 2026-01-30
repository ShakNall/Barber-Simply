<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        Shift::truncate();

        Shift::insert([
            [
                'name' => 'Shift 1',
                'start_time' => '09:00',
                'end_time' => '17:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Shift 2',
                'start_time' => '14:00',
                'end_time' => '22:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
