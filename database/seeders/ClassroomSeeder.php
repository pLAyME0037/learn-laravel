<?php

namespace Database\Seeders;

use App\Models\Classroom;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassroomSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF;');
        Classroom::truncate();
        DB::statement('PRAGMA foreign_keys = ON;');

        $buildings = [
            'A' => ['name' => 'Main Hall', 'floors' => 3, 'rooms_per_floor' => 5],
            'B' => ['name' => 'Science Block', 'floors' => 4, 'rooms_per_floor' => 6],
            'C' => ['name' => 'Engineering Wing', 'floors' => 2, 'rooms_per_floor' => 4],
            'D' => ['name' => 'Liberal Arts', 'floors' => 3, 'rooms_per_floor' => 5],
        ];

        foreach ($buildings as $code => $info) {
            for ($floor = 1; $floor <= $info['floors']; $floor++) {
                for ($room = 1; $room <= $info['rooms_per_floor']; $room++) {
                    
                    // Room Number Logic: A-101, B-205
                    $roomNum = sprintf("%s-%d%02d", $code, $floor, $room);
                    
                    // Type Logic
                    $type = 'Lecture Hall';
                    $capacity = 60;

                    if ($code === 'B' && $room > 4) {
                        $type = 'Laboratory'; // Science labs at end of hall
                        $capacity = 30;
                    } elseif ($code === 'C' && $floor === 1) {
                        $type = 'Workshop'; // Engineering workshops on ground floor
                        $capacity = 40;
                    }

                    Classroom::create([
                        'room_number'   => $roomNum,
                        'building_name' => $info['name'],
                        'type'          => $type,
                        'capacity'      => $capacity,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }
        }
    }
}