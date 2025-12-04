<?php

namespace Database\Seeders;

use App\Models\Gender;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF;');
        Gender::truncate();
        DB::statement('PRAGMA foreign_keys = ON;');

        $genders = [
            ['name' => 'Male', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Female', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Other', 'created_at' => now(), 'updated_at' => now()],
        ];

        Gender::insert($genders);
    }
}
