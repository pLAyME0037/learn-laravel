<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Computer Science',
                'code' => 'CS',
                'description' => 'Department of Computer Science and Information Technology',
                'email' => 'cs@university.edu',
                'phone' => '+1-555-0101',
                'office_location' => 'Tech Building, Room 301',
                'founded_year' => 1990,
                'budget' => 1500000.00,
                'website' => 'https://cs.university.edu',
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'name' => 'Electrical Engineering',
                'code' => 'EE',
                'description' => 'Department of Electrical and Electronics Engineering',
                'email' => 'ee@university.edu',
                'phone' => '+1-555-0102',
                'office_location' => 'Engineering Building, Room 201',
                'founded_year' => 1985,
                'budget' => 1200000.00,
                'website' => 'https://ee.university.edu',
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'name' => 'Business Administration',
                'code' => 'BA',
                'description' => 'Department of Business Administration and Management',
                'email' => 'ba@university.edu',
                'phone' => '+1-555-0103',
                'office_location' => 'Business Building, Room 101',
                'founded_year' => 1975,
                'budget' => 1800000.00,
                'website' => 'https://ba.university.edu',
                'is_active' => true,
                'display_order' => 3,
            ]
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(
                ['code' => $department['code']],
                $department
            );
        }
    }
}