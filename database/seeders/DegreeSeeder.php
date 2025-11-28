<?php

namespace Database\Seeders;

use App\Models\Degree;
use Illuminate\Database\Seeder;

class DegreeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Degree::create(['name' => 'Bachelor']);
        Degree::create(['name' => 'Master']);
        Degree::create(['name' => 'PhD']);
    }
}
