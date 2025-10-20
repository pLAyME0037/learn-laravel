<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Program;
use App\Models\Role;
use App\Models\User;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentManagementSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('name', 'student')->first();
        // Fetch departments after they have been seeded
        $csDepartment = Department::where('code', 'CS')->first();
        $eeDepartment = Department::where('code', 'EE')->first();
        // Fetch programs after they have been seeded
        $bscCs = Program::where('name', 'Bachelor of Science in Computer Science')->first();
        $bscEe = Program::where('name', 'Bachelor of Science in Electrical Engineering')->first();

        // Ensure departments and programs are available
        if (! $csDepartment) {
            $this->command->error('Computer Science department not found. Please ensure DepartmentSeeder ran successfully.');
            return;
        }
        if (! $eeDepartment) {
            $this->command->error('Electrical Engineering department not found. Please ensure DepartmentSeeder ran successfully.');
            return;
        }
        if (! $bscCs) {
            $this->command->error('Bachelor of Science in Computer Science program not found. Please ensure ProgramSeeder ran successfully.');
            return;
        }
        if (! $bscEe) {
            $this->command->error('Bachelor of Science in Electrical Engineering program not found. Please ensure ProgramSeeder ran successfully.');
            return;
        }

        $student1 = User::create([
            'name' => 'John Smith',
            'username' => 'johnsmith',
            'email' => 'john.smith@student.edu',
            'password' => Hash::make('password'),
            'department_id' => $csDepartment->id,
            'role_id' => $role->getKey(),
            'is_active' => true,
        ]);

        Student::create([
            'user_id' => $student1->id,
            'student_id' => 'STU-2024-ABC123',
            'department_id' => $eeDepartment->id,
            'program_id' => $bscCs->id,
            'date_of_birth' => '2000-05-15',
            'gender' => 'male',
            'nationality' => 'American',
            'phone' => '+1234567890',
            'emergency_contact_name' => 'Jane Smith',
            'emergency_contact_phone' => '+1234567891',
            'emergency_contact_relation' => 'Mother',
            'current_address' => '123 Main St, City, State',
            'permanent_address' => '123 Main St, City, State',
            'city' => 'New York',
            'state' => 'NY',
            'country' => 'USA',
            'postal_code' => '10001',
            'admission_date' => '2023-09-01',
            'expected_graduation' => '2027-06-01',
            'current_semester' => 2,
            'academic_status' => 'active',
            'enrollment_status' => 'full_time',
            'fee_category' => 'regular',
            'has_outstanding_balance' => false,
            'previous_education' => 'High School Diploma',
            'blood_group' => 'A+',
            'has_disability' => false,
        ]);

        $student2 = User::create([
            'name' => 'Sarah Johnson',
            'username' => 'sarahj',
            'email' => 'sarah.johnson@student.edu',
            'password' => Hash::make('password'),
            'department_id' => $csDepartment->id,
            'role_id' => $role->getKey(),
            'is_active' => true,
        ]);

        Student::create([
            'user_id' => $student2->id,
            'student_id' => 'STU-2024-DEF456',
            'department_id' => $csDepartment->id,
            'program_id' => $bscEe->id,
            'date_of_birth' => '2001-08-22',
            'gender' => 'female',
            'nationality' => 'Canadian',
            'phone' => '+1987654321',
            'emergency_contact_name' => 'Robert Johnson',
            'emergency_contact_phone' => '+1987654322',
            'emergency_contact_relation' => 'Father',
            'current_address' => '456 Oak St, City, State',
            'permanent_address' => '456 Oak St, City, State',
            'city' => 'Toronto',
            'state' => 'ON',
            'country' => 'Canada',
            'postal_code' => 'M5V 2T6',
            'admission_date' => '2023-09-01',
            'expected_graduation' => '2027-06-01',
            'current_semester' => 2,
            'academic_status' => 'active',
            'enrollment_status' => 'full_time',
            'fee_category' => 'scholarship',
            'has_outstanding_balance' => false,
            'previous_education' => 'High School Diploma',
            'blood_group' => 'O+',
            'has_disability' => false,
        ]);
    }
}
