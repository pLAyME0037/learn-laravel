<?php

namespace Database\Seeders;

use App\Models\SystemConfig;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemConfig::firstOrCreate([
            'key' => 'app_name',
            'value' => 'Student Management System',
        ]);

        SystemConfig::firstOrCreate([
            'key' => 'default_currency',
            'value' => 'USD',
        ]);

        SystemConfig::firstOrCreate([
            'key' => 'timezone',
            'value' => 'UTC',
        ]);

        SystemConfig::firstOrCreate([
            'key' => 'pagination_limit',
            'value' => '15',
        ]);

        SystemConfig::firstOrCreate([
            'key' => 'email_from_address',
            'value' => 'noreply@example.com',
        ]);

        SystemConfig::firstOrCreate([
            'key' => 'email_from_name',
            'value' => 'Student Management System',
        ]);

        SystemConfig::firstOrCreate([
            'key' => 'academic_year_start_month',
            'value' => '9', // September
        ]);
    }
}
