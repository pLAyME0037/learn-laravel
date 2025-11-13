<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Faculty; // Import Faculty model
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing faculty IDs to assign departments to them
        $facultyIds = Faculty::pluck('id')->toArray();

        // Ensure faculties exist before seeding departments
        if (empty($facultyIds)) {
            $this->command->warn('No faculties found. Please seed faculties first.');
            return;
        }

        $departments = [
            [
                'name' => 'Computer Science',
                'code' => 'CS',
                'description' => 'Department of Computer Science and Information Technology',
                'faculty_name' => 'Faculty of Science', // Added faculty name for lookup
                'email' => 'cs@university.edu',
                'phone' => '+1-555-0101',
                'office_location' => 'Tech Building, Room 301',
                'established_year' => 1990,
                'budget' => 1500000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Electrical Engineering',
                'code' => 'EE',
                'description' => 'Department of Electrical and Electronics Engineering',
                'faculty_name' => 'Faculty of Engineering', // Added faculty name for lookup
                'email' => 'ee@university.edu',
                'phone' => '+1-555-0102',
                'office_location' => 'Engineering Building, Room 201',
                'established_year' => 1985,
                'budget' => 1200000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Business Administration',
                'code' => 'BA',
                'description' => 'Department of Business Administration and Management',
                'faculty_name' => 'Faculty of Business', // Added faculty name for lookup
                'email' => 'ba@university.edu',
                'phone' => '+1-555-0103',
                'office_location' => 'Business Building, Room 101',
                'established_year' => 1975,
                'budget' => 1800000.00,
                'is_active' => true,
            ],
            // Add more departments as needed, ensuring they map to existing faculties
            [
                'name' => 'Mathematics',
                'code' => 'MATH',
                'description' => 'Department of Mathematics',
                'faculty_name' => 'Faculty of Science', // Assign to Faculty of Science
                'email' => 'math@university.edu',
                'phone' => '+1-555-0104',
                'office_location' => 'Science Building, Room 401',
                'established_year' => 1992,
                'budget' => 1100000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Mechanical Engineering',
                'code' => 'ME',
                'description' => 'Department of Mechanical Engineering',
                'faculty_name' => 'Faculty of Engineering', // Assign to Faculty of Engineering
                'email' => 'me@university.edu',
                'phone' => '+1-555-0105',
                'office_location' => 'Engineering Building, Room 301',
                'established_year' => 1988,
                'budget' => 1300000.00,
                'is_active' => true,
            ],
        ];

        foreach ($departments as $departmentData) {
            // Find the faculty ID for the department
            $faculty = Faculty::where('name', $departmentData['faculty_name'])->first();

            if (!$faculty) {
                $this->command->warn(''
                . "Faculty '{$departmentData['faculty_name']}' not found."
                . "Skipping department: {$departmentData['name']}"
            );
                continue;
            }

            // Prepare data for creation, ensuring faculty_id is set and faculty_name is removed
            $createData = $departmentData;
            $createData['faculty_id'] = $faculty->id;
            unset($createData['faculty_name']);

            Department::firstOrCreate(
                ['code' => $departmentData['code']],
                $createData
            );
        }
    }
}
