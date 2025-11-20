<?php
namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\ClassSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing attendance records to prevent duplicates on re-seeding
        DB::table('attendances')->truncate();

        // Ensure there are users and class schedules to link to
        if (User::count() === 0 || ClassSchedule::count() === 0) {
            $this->command->warn('Please run UserSeeder and ClassScheduleSeeder first.');
            return;
        }

        // Create a number of attendance records using the factory
        Attendance::factory()->count(50)->create();

        $this->command->info('Attendance records seeded successfully.');
    }
}
