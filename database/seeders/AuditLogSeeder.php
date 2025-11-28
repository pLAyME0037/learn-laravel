<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema; // Import Schema

class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Database Agnostic Way to Disable Foreign Keys
        Schema::disableForeignKeyConstraints();
        AuditLog::truncate();
        Schema::enableForeignKeyConstraints();

        // 2. Fetch IDs
        $userIds = User::pluck('id')->toArray();

        if (empty($userIds)) {
            $this->command->warn('No users found. Run Student/User seeders first.');
            return;
        }

        $logsToInsert = [];
        $now = now();
        $actions = ['created', 'updated', 'deleted', 'logged_in', 'logged_out'];

        // 3. Generate Data
        foreach ($userIds as $userId) {
            $count = rand(1, 5);
            
            for ($i = 0; $i < $count; $i++) {
                $action = $actions[array_rand($actions)];
                
                $logsToInsert[] = [
                    'user_id'     => $userId,
                    'action'      => $action,
                    'description' => "User ID {$userId} performed {$action}",
                    'created_at'  => $now->subMinutes(rand(1, 10000)),
                    'updated_at'  => $now,
                ];
            }
        }

        // 4. Bulk Insert
        foreach (array_chunk($logsToInsert, 1000) as $chunk) {
            AuditLog::insert($chunk);
        }

        $this->command->info('Audit logs seeded successfully.');
    }
}