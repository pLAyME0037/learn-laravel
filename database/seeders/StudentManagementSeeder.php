<?php
namespace Database\Seeders;

use App\Models\Department;
use App\Models\Gender;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
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
        $password     = Hash::make('password'); // Hash once, use everywhere
        $studentCount = 50;

                                                    // 1. Fetch Maps (ID Lookups)
        $genders  = Gender::pluck('id')->toArray(); // Just IDs: [1, 2]
        $programs = Program::with('major.department')
            ->whereHas('major.department')
            ->get();

        if ($programs->isEmpty() || empty($genders)) {
            $this->command->error('Missing Programs or Genders.');
            return;
        }

        // 2. Fetch Role ID for "student" (for pivot table insert)
        $studentRoleId = Role::where('name', 'student')->value('id');

        // --- PHASE A: Bulk Create Users ---
        $usersData = [];
        $userIds   = []; // Placeholder logic won't give us IDs back easily with insert(), so we must manage this carefully.

        // Strategy: Insert Users -> Get Last ID -> Calculate Range
        // This is tricky with bulk inserts.
        // FASTEST SAFE WAY: Use 'create' for User (to get ID) but keep it light?
        // OR: Insert chunk, then query back?
        // ACTUALLY: 21s is mostly 'Hash::make'. Since we cached hash, loops are faster now.
        // Let's stick to a loop for Users to get IDs safely, but optimize the Student insert.

        $studentsData      = [];
        $modelHasRolesData = []; // For manual pivot insert

        // Pre-calculate random choices to save CPU cycles
        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
        $feeCats     = ['regular', 'scholarship', 'international'];

        DB::beginTransaction(); // Speed up transaction

        for ($i = 0; $i < $studentCount; $i++) {
            // Pick Program
            $program      = $programs->random();
            $departmentId = $program->major->department->id;

            // User Data
            $firstName = $faker->firstName;
            $lastName  = $faker->lastName;
            $username  = strtolower($firstName . '.' . $lastName . rand(100, 999));

            // Creating User individually is necessary to get the ID for the Student record
            // But since we pre-hashed the password, this is much faster.
            $userId = DB::table('users')->insertGetId([
                'name'              => "$firstName $lastName",
                'username'          => $username,
                'email'             => "$username@student.edu",
                'password'          => $password,
                'is_active'         => true,
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);

            // Prepare Role Data (Manual Pivot Insert)
            if ($studentRoleId) {
                $modelHasRolesData[] = [
                    'role_id'    => $studentRoleId,
                    'model_type' => 'App\Models\User',
                    'model_id'   => $userId,
                ];
            }

            // Prepare Student Data (Bulk Array)
            $studentsData[] = [
                'user_id'                    => $userId,
                'student_id'                 => 'STU-' . $now->year . '-' . str_pad((string) rand(1, 99999), 5, '0', STR_PAD_LEFT),
                'department_id'              => $departmentId,
                'program_id'                 => $program->id,
                'date_of_birth'              => $faker->dateTimeBetween('-25 years', '-18 years'),
                'gender_id'                  => $genders[array_rand($genders)],
                'nationality'                => 'United States', // Static is faster than faker->country
                'phone'                      => '123-456-7890',  // Static/Simple is faster
                'id_card_number'             => rand(100000000, 999999999),
                'emergency_contact_name'     => "Parent of $firstName",
                'emergency_contact_phone'    => '987-654-3210',
                'emergency_contact_relation' => 'Parent',
                'current_address'            => '123 Campus Dr',
                'permanent_address'          => '123 Campus Dr',
                'city'                       => 'Cityville',
                'district'                   => 'Central',
                'commune'                    => 'Sector 1',
                'village'                    => 'Village A',
                'postal_code'                => '12345',
                'semester'                   => 'Semester ' . rand(1, 8),
                'admission_date'             => $now,
                'expected_graduation'        => $now->copy()->addYears(4),
                'current_semester'           => rand(1, 8),
                'academic_status'            => 'active',
                'enrollment_status'          => 'full_time',
                'fee_category'               => $feeCats[array_rand($feeCats)],
                'blood_group'                => $bloodGroups[array_rand($bloodGroups)],
                'has_disability'             => 0,
                'has_outstanding_balance'    => rand(0, 1),
                'created_at'                 => $now,
                'updated_at'                 => $now,
            ];
        }

        // Bulk Insert Students
        foreach (array_chunk($studentsData, 100) as $chunk) {
            foreach ($chunk as $studentData) {
                Student::create($studentData);
            }
        }

        // Bulk Insert Roles
        if (! empty($modelHasRolesData)) {
            DB::table('model_has_roles')->insert($modelHasRolesData);
        }

        DB::commit();
    }
}
