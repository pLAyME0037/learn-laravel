<?php

namespace Database\Seeders;

use App\Models\Classroom;
use Illuminate\Database\Seeder;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classrooms = [
            ['room_number' => '101', 'capacity' => 30],
            ['room_number' => '102', 'capacity' => 25],
            ['room_number' => 'Lab A', 'capacity' => 20],
            ['room_number' => 'Lab B', 'capacity' => 20],
            ['room_number' => 'Auditorium', 'capacity' => 100],
        ];

        foreach ($classrooms as $classroom) {
            Classroom::create($classroom);
        }
    }
}
