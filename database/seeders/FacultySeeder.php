<?php

namespace Database\Seeders;

use App\Models\Faculty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FacultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Faculty::create([
            'name' => 'Faculty of Science',
        ]);

        Faculty::create([
            'name' => 'Faculty of Engineering',
        ]);

        Faculty::create([
            'name' => 'Faculty of Business',
        ]);

        Faculty::create([
            'name' => 'Faculty of Arts and Humanities',
        ]);

        Faculty::create([
            'name' => 'Faculty of Medicine',
        ]);
    }
}
