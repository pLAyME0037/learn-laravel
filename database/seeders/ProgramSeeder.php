<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        // You can add other departments and their programs here.
        $programsByDepartment = [
            'Computer Science' => [
                [
                    'name' => 'Bachelor of Science in Computer Science',
                    'code' => 'BSCS',
                    'level' => 'bachelor',
                    'duration_years' => 4,
                    'total_credits_required' => 120,
                    'tuition_fee' => 15000.00,
                ],
                [
                    'name' => 'Master of Science in Computer Science',
                    'code' => 'MSCS',
                    'level' => 'master',
                    'duration_years' => 2,
                    'total_credits_required' => 60,
                    'tuition_fee' => 20000.00,
                ],
            ],
            'Electrical Engineering' => [
                [
                    'name' => 'Bachelor of Science in Electrical Engineering',
                    'code' => 'BSEE',
                    'level' => 'bachelor',
                    'duration_years' => 4,
                    'total_credits_required' => 120,
                    'tuition_fee' => 16000.00,
                ],
            ],
            // Add more departments and programs as needed
        ];

        foreach ($programsByDepartment as $departmentName => $programs) {
            $department = Department::where('name', $departmentName)->first();

            if ($department) {
                foreach ($programs as $program) {
                    Program::updateOrCreate(
                        ['code' => $program['code']],
                        array_merge($program, [
                            'department_id' => $department->id,
                            'description' => "{$program['name']} program in {$department->name}",
                            'is_active' => true,
                        ])
                    );
                }
            }
        }
    }
}
