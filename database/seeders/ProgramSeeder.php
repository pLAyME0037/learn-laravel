<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Program;
use App\Models\Faculty; // Import Faculty model
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing faculty IDs to assign programs to them
        $facultyIds = Faculty::pluck('id')->toArray();

        // Ensure there are faculties to assign programs to
        if (empty($facultyIds)) {
            $this->command->warn('No faculties found. Please seed faculties first.');
            return;
        }

        // Define programs with their associated department codes and other details
        $programsData = [
            'Faculty of Science' => [
                [
                    'department_code' => 'CS', // Use department code for lookup
                    'name' => 'Bachelor of Science in Computer Science',
                    'code' => 'BSCS',
                    'level' => 'bachelor',
                    'duration_years' => 4,
                    'total_semesters' => 8,
                    'total_credits_required' => 120,
                    'tuition_fee' => 15000.00,
                    'description' => 'A comprehensive program covering core Computer Science principles.',
                    'is_active' => true,
                ],
                [
                    'department_code' => 'CS', // Use department code for lookup
                    'name' => 'Master of Science in Computer Science',
                    'code' => 'MSCS',
                    'level' => 'master',
                    'duration_years' => 2,
                    'total_semesters' => 4,
                    'total_credits_required' => 60,
                    'tuition_fee' => 20000.00,
                    'description' => 'Advanced studies in Computer Science for graduate students.',
                    'is_active' => true,
                ],
                [
                    'department_code' => 'MATH', // Use department code for lookup
                    'name' => 'Bachelor of Science in Mathematics',
                    'code' => 'BSMATH',
                    'level' => 'bachelor',
                    'duration_years' => 4,
                    'total_semesters' => 8,
                    'total_credits_required' => 120,
                    'tuition_fee' => 14000.00,
                    'description' => 'Focuses on theoretical and applied mathematics.',
                    'is_active' => true,
                ],
            ],
            'Faculty of Engineering' => [
                [
                    'department_code' => 'EE', // Use department code for lookup
                    'name' => 'Bachelor of Science in Electrical Engineering',
                    'code' => 'BSEE',
                    'level' => 'bachelor',
                    'duration_years' => 4,
                    'total_semesters' => 8,
                    'total_credits_required' => 120,
                    'tuition_fee' => 16000.00,
                    'description' => 'Covers the principles and applications of electrical systems.',
                    'is_active' => true,
                ],
                [
                    'department_code' => 'ME', // Use department code for lookup
                    'name' => 'Bachelor of Science in Mechanical Engineering',
                    'code' => 'BSME',
                    'level' => 'bachelor',
                    'duration_years' => 4,
                    'total_semesters' => 8,
                    'total_credits_required' => 120,
                    'tuition_fee' => 16500.00,
                    'description' => 'Focuses on the design, analysis, and manufacturing of mechanical systems.',
                    'is_active' => true,
                ],
            ],
            'Faculty of Business' => [
                [
                    'department_code' => 'BA', // Use department code for lookup
                    'name' => 'Bachelor of Business Administration',
                    'code' => 'BBA',
                    'level' => 'bachelor',
                    'duration_years' => 4,
                    'total_semesters' => 8,
                    'total_credits_required' => 120,
                    'tuition_fee' => 13000.00,
                    'description' => 'Provides a broad foundation in business principles.',
                    'is_active' => true,
                ],
            ],
        ];

        foreach ($programsData as $facultyName => $programs) {
            $faculty = Faculty::where('name', $facultyName)->first();
            
            if ($faculty) {
                foreach ($programs as $programData) {
                    // Find the department using its code
                    $departmentCode = $programData['department_code'];
                    $department = Department::where('code', $departmentCode)->firstOrFail();

                    // Remove department_code from programData as it's not a column in the programs table
                    unset($programData['department_code']);

                    // Create the program, linking it to the department and faculty
                    Program::updateOrCreate(
                        ['code' => $programData['code']],
                        array_merge($programData, [
                            'department_id' => $department->id,
                            // Removed faculty_id as programs table does not have this column directly.
                            // Faculty can be accessed via the department relationship.
                        ])
                    );
                }
            } else {
                $this->command->warn("Faculty '{$facultyName}' not found. Skipping programs for this faculty.");
            }
        }
    }
}
