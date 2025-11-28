<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Degree;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\Program;
use App\Models\AcademicYear; // Added
use App\Models\Semester;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AcademicStructureSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Clean Slate
        DB::statement('PRAGMA foreign_keys = OFF;');
        AcademicYear::truncate(); // Added
        Degree::truncate();
        Faculty::truncate();
        Department::truncate();
        Major::truncate();
        Program::truncate();
        Course::truncate();
        Semester::truncate();
        DB::statement('PRAGMA foreign_keys = ON;');

        $now = now();

        // Ensure an Academic Year exists for semesters
        $academicYear = AcademicYear::firstOrCreate(
            ['name' => '2025'], // Use 'name' instead of 'year'
            [
                'start_date' => $now->copy()->startOfYear(),
                'end_date' => $now->copy()->endOfYear(),
                'is_current' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
        $academicYearId = $academicYear->id;

        // ------------------------------------------------------------------
        // PHASE 1: Global Lookups
        // ------------------------------------------------------------------
        $degrees = [
            ['name' => 'Bachelor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Master', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'PhD', 'created_at' => $now, 'updated_at' => $now],
        ];
        Degree::insert($degrees);
        $degreeMap = Degree::pluck('id', 'name')->toArray();

        $semesters = [
            [
                'academic_year_id' => $academicYearId, // Added
                'name' => 'Fall 2025', 
                'start_date' => $now->copy()->addMonths(1), 
                'end_date' => $now->copy()->addMonths(5), 
                'is_active' => true, 'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'academic_year_id' => $academicYearId, // Added
                'name' => 'Spring 2026', 
                'start_date' => $now->copy()->addMonths(6), 
                'end_date' => $now->copy()->addMonths(10), 
                'is_active' => false, 
                'created_at' => $now, 
                'updated_at' => $now
            ],
        ];
        Semester::insert($semesters);
        $semesterId = Semester::first()->id;

        // ------------------------------------------------------------------
        // PHASE 2: The Complete Data Structure
        // ------------------------------------------------------------------
        $structure = [
            'Faculty of Engineering & Technology' => [
                'Computer Science' => [
                    'Software Engineering',
                    'Artificial Intelligence',
                    'Cyber Security',
                    'Data Science',
                    'Network Engineering',
                    'Information Systems',
                    'Game Development',
                    'Cloud Computing'
                ],
                'Electrical Engineering' => [
                    'Power Systems',
                    'Electronics & Telecommunication',
                    'Mechatronics',
                    'Control Systems',
                    'Renewable Energy',
                    'Signal Processing'
                ],
                'Civil Engineering' => [
                    'Structural Engineering',
                    'Geotechnical Engineering',
                    'Transportation Engineering',
                    'Construction Management',
                    'Environmental Engineering',
                    'Urban Planning'
                ],
                'Mechanical Engineering' => [
                    'Automotive Engineering',
                    'Thermal Engineering',
                    'Manufacturing Systems',
                    'Robotics',
                    'Aerospace Engineering',
                    'Fluid Dynamics'
                ],
                'Chemical Engineering' => [
                    'Process Engineering',
                    'Materials Science',
                    'Biochemical Engineering',
                    'Petroleum Engineering'
                ]
            ],
            'Faculty of Business & Economics' => [
                'Business Administration' => [
                    'Marketing Management',
                    'Human Resource Management',
                    'Supply Chain Management',
                    'International Business',
                    'Entrepreneurship',
                    'Operations Management',
                    'Strategic Management'
                ],
                'Accounting & Finance' => [
                    'Public Accounting',
                    'Corporate Finance',
                    'Banking & Insurance',
                    'Taxation',
                    'FinTech',
                    'Risk Management',
                    'Investment Analysis'
                ],
                'Economics' => [
                    'Development Economics',
                    'International Economics',
                    'Econometrics',
                    'Behavioral Economics',
                    'Public Policy'
                ]
            ],
            'Faculty of Health Sciences' => [
                'Nursing' => [
                    'Adult Health Nursing',
                    'Pediatric Nursing',
                    'Critical Care Nursing',
                    'Community Health',
                    'Mental Health Nursing',
                    'Midwifery'
                ],
                'Medicine' => [
                    'General Medicine',
                    'Surgery',
                    'Pediatrics',
                    'Internal Medicine',
                    'Cardiology',
                    'Neurology'
                ],
                'Pharmacy' => [
                    'Clinical Pharmacy',
                    'Pharmaceutical Chemistry',
                    'Pharmacology',
                    'Toxicology',
                    'Industrial Pharmacy'
                ],
                'Public Health' => [
                    'Epidemiology',
                    'Health Policy & Management',
                    'Environmental Health',
                    'Nutrition & Dietetics',
                    'Biostatistics'
                ],
                'Dentistry' => [
                    'Oral Surgery',
                    'Orthodontics',
                    'Periodontology',
                    'Prosthodontics'
                ]
            ],
            'Faculty of Arts & Humanities' => [
                'English Language & Literature' => [
                    'English Literature',
                    'Linguistics',
                    'Creative Writing',
                    'Teaching English as a Second Language (TESL)',
                    'Comparative Literature'
                ],
                'History & Political Science' => [
                    'Modern History',
                    'Political Science',
                    'International Relations',
                    'Archaeology',
                    'Ancient History',
                    'Public Administration'
                ],
                'Psychology' => [
                    'Clinical Psychology',
                    'Counseling Psychology',
                    'Industrial-Organizational Psychology',
                    'Developmental Psychology',
                    'Forensic Psychology'
                ],
                'Sociology & Anthropology' => [
                    'Sociology',
                    'Cultural Anthropology',
                    'Social Work',
                    'Criminology'
                ],
                'Philosophy & Religion' => [
                    'Philosophy',
                    'Ethics',
                    'Religious Studies',
                    'Logic'
                ]
            ],
            'Faculty of Science' => [
                'Biological Sciences' => [
                    'Microbiology',
                    'Genetics',
                    'Biotechnology',
                    'Botany',
                    'Zoology',
                    'Marine Biology',
                    'Bioinformatics'
                ],
                'Chemistry' => [
                    'Organic Chemistry',
                    'Analytical Chemistry',
                    'Industrial Chemistry',
                    'Physical Chemistry',
                    'Inorganic Chemistry'
                ],
                'Physics & Mathematics' => [
                    'Theoretical Physics',
                    'Applied Mathematics',
                    'Statistics',
                    'Astrophysics',
                    'Actuarial Science',
                    'Nuclear Physics'
                ],
                'Earth & Environmental Sciences' => [
                    'Geology',
                    'Environmental Science',
                    'Meteorology',
                    'Oceanography'
                ]
            ],
            'Faculty of Law' => [
                'Law' => [
                    'Civil Law',
                    'Criminal Law',
                    'International Law',
                    'Corporate Law',
                    'Constitutional Law',
                    'Human Rights Law'
                ]
            ],
            'Faculty of Architecture & Design' => [
                'Architecture' => [
                    'Architectural Design',
                    'Landscape Architecture',
                    'Urban Design',
                    'Interior Architecture'
                ],
                'Design' => [
                    'Graphic Design',
                    'Industrial Design',
                    'Fashion Design',
                    'Multimedia Arts',
                    'Animation'
                ]
            ],
            'Faculty of Education' => [
                'Education' => [
                    'Primary Education',
                    'Secondary Education',
                    'Educational Leadership',
                    'Special Education',
                    'Curriculum Design'
                ]
            ]
        ];

        // ------------------------------------------------------------------
        // PHASE 3: Faculties
        // ------------------------------------------------------------------
        $facultyData = [];
        foreach (array_keys($structure) as $name) {
            $facultyData[] = ['name' => $name, 'created_at' => $now, 'updated_at' => $now];
        }
        Faculty::insert($facultyData);
        $facultyMap = Faculty::pluck('id', 'name')->toArray();

        // ------------------------------------------------------------------
        // PHASE 4: Departments
        // ------------------------------------------------------------------
        $deptData = [];
        foreach ($structure as $facultyName => $departments) {
            $facId = $facultyMap[$facultyName];
            foreach (array_keys($departments) as $deptName) {
                $deptData[] = [
                    'name' => $deptName,
                    'code' => $this->generateCode($deptName),
                    'description' => "Department of $deptName",
                    'faculty_id' => $facId,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }
        }
        Department::insert($deptData);
        $deptMap = Department::pluck('id', 'name')->toArray();

        // ------------------------------------------------------------------
        // PHASE 5: Majors
        // ------------------------------------------------------------------
        $majorData = [];
        foreach ($structure as $facName => $depts) {
            foreach ($depts as $deptName => $majors) {
                $deptId = $deptMap[$deptName] ?? null;
                if (!$deptId) continue;

                foreach ($majors as $majorName) {
                    $majorData[] = [
                        'name' => $majorName,
                        'department_id' => $deptId,
                        'degree_id' => $degreeMap['Bachelor'], // Default
                        'cost' => rand(1000, 5000),
                        'payment_frequency' => 'term', // Changed from 'semester' to 'term'
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                }
            }
        }
        foreach (array_chunk($majorData, 100) as $chunk) {
            Major::insert($chunk);
        }
        $majorMap = Major::pluck('id', 'name')->toArray();

        // ------------------------------------------------------------------
        // PHASE 6: Programs
        // ------------------------------------------------------------------
        $programData = [];
        foreach ($majorMap as $majorName => $majorId) {
            // Bachelor
            $programData[] = [
                'name' => "Bachelor of $majorName",
                'major_id' => $majorId,
                'degree_id' => $degreeMap['Bachelor'],
                'created_at' => $now, 'updated_at' => $now
            ];
            // Master (30% chance)
            if (rand(1, 100) <= 30) {
                $programData[] = [
                    'name' => "Master of $majorName",
                    'major_id' => $majorId,
                    'degree_id' => $degreeMap['Master'],
                    'created_at' => $now, 'updated_at' => $now
                ];
            }
        }
        foreach (array_chunk($programData, 100) as $chunk) {
            Program::insert($chunk);
        }
        $programs = Program::select('id', 'major_id')->get();

        // ------------------------------------------------------------------
        // PHASE 7: Courses
        // ------------------------------------------------------------------
        $courseData = [];
        $majorToDeptMap = Major::pluck('department_id', 'id')->toArray();
        $deptToFacMap = Department::pluck('faculty_id', 'id')->toArray();

        foreach ($programs as $program) {
            $majorId = $program->major_id;
            $deptId = $majorToDeptMap[$majorId] ?? null;
            if (!$deptId) continue;
            $facId = $deptToFacMap[$deptId] ?? null;

            // Create 4 courses per program
            for ($i = 1; $i <= 4; $i++) {
                $level = $i * 100;
                $courseData[] = [
                    'name' => "Course Level $level for Program " . $program->id,
                    'code' => "CRS-" . $program->id . "-$level",
                    'credits' => rand(3, 4),
                    'max_students' => 60,
                    'start_date' => $now->copy()->addDays(10),
                    'end_date' => $now->copy()->addMonths(4),
                    'status' => 'active',
                    'department_id' => $deptId,
                    'faculty_id' => $facId,
                    'program_id' => $program->id,
                    'semester_id' => $semesterId,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }
        }

        foreach (array_chunk($courseData, 200) as $chunk) {
            Course::insert($chunk);
        }
    }

    private function generateCode(string $string): string
    {
        $string = strtoupper($string);
        // Split by common delimiters (space, &, -, /, \) and remove empty matches
        $words = array_filter(preg_split('/[\s\&\-\/\\\\]+/', $string), fn($w) => !empty($w) && !in_array($w, ['OF', 'AND']));
        
        $code = '';

        if (count($words) < 2) { // Changed this condition
            // If no significant words or only one word, use a slug from the original string or first word
            if (!empty($words)) {
                $code = substr($words[0], 0, 4);
            } else {
                return strtoupper(substr(Str::slug($string), 0, 4));
            }
        } else {
            // For multi-word departments (count($words) >= 2)
            // Take first two letters of the first word
            $code .= substr($words[0], 0, 2);
            // Then take first letter of subsequent words
            for ($i = 1; $i < count($words); $i++) {
                $code .= substr($words[$i], 0, 1);
            }
            // Ensure a reasonable max length
            $code = substr($code, 0, 5); // Limit to 5 characters
        }
        
        // Final fallback if still too short (e.g. from very short words)
        if (strlen($code) < 3) {
            return strtoupper(substr(Str::slug($string), 0, 4));
        }

        return $code;
    }
}
