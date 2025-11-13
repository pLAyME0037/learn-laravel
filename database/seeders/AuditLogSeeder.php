<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuditLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing audit logs to prevent duplicates on re-seeding
        DB::table('audit_logs')->truncate();

        // Get all users to associate audit logs with
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->info('No users found. Please run UserSeeder first.');
            return;
        }

        $actions = ['created', 'updated', 'deleted', 'logged_in', 'logged_out'];

        foreach ($users as $user) {
            // Create a few audit logs for each user
            for ($i = 0; $i < rand(1, 5); $i++) {
                AuditLog::create([
                    'user_id' => $user->id,
                    'action' => $actions[array_rand($actions)],
                    'description' => 'User ' . $user->name . ' performed an action.',
                ]);
            }
        }

        $this->command->info('Audit logs seeded successfully.');
    }
}
