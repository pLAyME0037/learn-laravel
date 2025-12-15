<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Instructor;
use App\Models\Role;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InstructorSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $password = Hash::make('password'); // Pre-hash for speed
        
        $departments = Department::pluck('id')->toArray();
        // Get Role ID (Spatie) directly to avoid model overhead in loop
        $staffRoleId = DB::table('roles')->where('name', 'staff')->value('id');

        if (empty($departments)) {
            $this->command->error('No Departments found. Run AcademicStructureSeeder first.');
            return;
        }

        // Create 20 Instructors
        for ($i = 1; $i <= 20; $i++) {
            
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;
            $username = strtolower($firstName . '.' . $lastName . rand(100, 999));

            // 1. Create User
            $userId = DB::table('users')->insertGetId([
                'name' => "Dr. $firstName $lastName",
                'username' => $username,
                'email' => "$username@university.edu",
                'password' => $password,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign Role
            if ($staffRoleId) {
                DB::table('model_has_roles')->insert([
                    'role_id' => $staffRoleId,
                    'model_type' => 'App\Models\User',
                    'model_id' => $userId,
                ]);
            }

            // 2. Create Instructor Profile
            $instructor = Instructor::create([
                'user_id' => $userId,
                'department_id' => $departments[array_rand($departments)], // Random Dept
                'staff_id' => 'STF-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'attributes' => [
                    'specialization' => $faker->jobTitle,
                    'office_hours' => 'Mon-Wed 2pm-4pm'
                ]
            ]);

            // 3. Contact
            $instructor->contactDetail()->create([
                'phone' => $faker->phoneNumber(),
                'emergency_name' => $faker->name,
                'emergency_phone' => $faker->phoneNumber(),
            ]);

            // 4. Address
            $instructor->address()->create([
                'current_address' => $faker->streetAddress,
                'postal_code' => $faker->postcode,
            ]);
        }
    }
}