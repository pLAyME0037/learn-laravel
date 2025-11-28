<?php
namespace Database\Seeders;

use App\Models\ContactDetail;
use App\Models\Instructor;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContactDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get IDs for users
        $userIds = User::pluck('id')->toArray();
        $data = [];
        $now = now();

        // Ensure necessary related data exists
        if (empty($userIds)) {
            $this->command->warn('No users found. Please seed them first.');
            return;
        }

        // Seed contact details for Users
        foreach ($userIds as $userId) {
            $data[] = [
                'person_id'    => $userId, // Use person_id as per migration
                'email'        => fake()->unique()->safeEmail(),
                'phone_number' => fake()->phoneNumber(),
                'address'      => fake()->address(),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($data, 500) as $chunk) {
            ContactDetail::insert($chunk);
        }
    }
}
