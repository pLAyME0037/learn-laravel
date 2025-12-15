<?php

namespace Database\Seeders;

use App\Models\Dictionary; // Or Gender model if you kept it
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\Location\Village;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentManagementSeeder extends Seeder
{
    public function run(): void
    {
        $faker        = Faker::create();
        $now          = now();
        // Pre-hash password for performance
        $passwordHash = Hash::make('password');
        $studentCount = 50;

        // 1. Fetch Dependencies
        // Use Dictionary keys if using that system, or IDs if using Tables
        // Assuming Dictionary based on your recent code:
        $genders = ['male', 'female']; 
        // If using Gender Table: $genders = \App\Models\Gender::pluck('id')->toArray();

        $programs = Program::with('major.department')->get();
        $villageIds = Village::inRandomOrder()->limit(100)->pluck('id')->toArray();

        if ($programs->isEmpty()) {
            $this->command->error('Missing Programs. Run AcademicStructureSeeder first.');
            return;
        }

        // Get Role ID (Spatie)
        $studentRoleId = DB::table('roles')->where('name', 'student')->value('id');

        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
        
        DB::beginTransaction();

        for ($i = 0; $i < $studentCount; $i++) {
            $program = $programs->random();
            
            // Calculate Progress
            $currentTerm = rand(1, 8); // Term 1-8
            $yearLevel   = ceil($currentTerm / 2); // 1-4

            // User Data
            $firstName = $faker->firstName;
            $lastName  = $faker->lastName;
            $username  = strtolower($firstName . '.' . $lastName . rand(100, 999));

            // 2. Create User (Raw DB insert is faster for seeders)
            $userId = DB::table('users')->insertGetId([
                'name'              => "$firstName $lastName",
                'username'          => $username,
                'email'             => "$username@student.edu",
                'password'          => $passwordHash,
                'is_active'         => true,
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);

            // Assign Role
            if ($studentRoleId) {
                DB::table('model_has_roles')->insert([
                    'role_id'    => $studentRoleId,
                    'model_type' => 'App\Models\User',
                    'model_id'   => $userId,
                ]);
            }

            // 3. Create Student
            // Note: We move personal details to 'attributes' JSON if your schema uses it
            $attributes = [
                'dob'         => $faker->dateTimeBetween('-25 years', '-18 years')->format('Y-m-d'),
                'gender'      => $genders[array_rand($genders)],
                'nationality' => 'Cambodian', // Example
                'blood_group' => $bloodGroups[array_rand($bloodGroups)],
            ];

            // Encrypt Sensitive Data (if your schema requires it, otherwise nullable)
            // Here we skip encryption for seeder speed unless strictly enforced by Model Accessor
            
            $studentIdStr = 'STU-' . $now->year . '-' . str_pad((string) rand(1, 99999), 5, '0', STR_PAD_LEFT);

            $student = Student::create([
                'user_id'                 => $userId,
                'program_id'              => $program->id,
                'student_id'              => $studentIdStr,
                
                // Academic Progress
                'year_level'              => $yearLevel,
                'current_term'            => $currentTerm,
                'academic_status'         => 'active',
                
                // Store JSON Attributes
                'attributes'              => $attributes,
                
                // Others (if columns exist in your specific migration version)
                'cgpa'                    => $faker->randomFloat(2, 2.0, 4.0),
                'has_outstanding_balance' => (bool)rand(0, 1),
                
                'created_at'              => $now,
                'updated_at'              => $now,
            ]);

            // 4. Contact Detail (Polymorphic)
            $student->contactDetail()->create([
                'phone'              => $faker->phoneNumber(),
                'emergency_name'     => "Parent of $firstName",
                'emergency_phone'    => $faker->phoneNumber(),
                'emergency_relation' => 'Parent',
            ]);

            // 5. Address (Polymorphic)
            $student->address()->create([
                'current_address' => $faker->streetAddress,
                'postal_code'     => $faker->postcode,
                'village_id'      => !empty($villageIds) ? $villageIds[array_rand($villageIds)] : null,
            ]);
        }

        DB::commit();
    }
}