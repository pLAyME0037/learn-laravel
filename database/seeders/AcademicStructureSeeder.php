<?php
namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\Degree;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\Program;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcademicStructureSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Clean Slate (Disable Foreign Keys for speed)
        DB::statement('PRAGMA foreign_keys = OFF;');

        // Truncate tables in dependency order
        DB::table('program_structures')->truncate();
        ClassSession::truncate();
        Enrollment::truncate();
        Course::truncate();
        Program::truncate();
        Major::truncate();
        Department::truncate();
        Faculty::truncate();
        Semester::truncate();
        Degree::truncate();
        AcademicYear::truncate();

        DB::statement('PRAGMA foreign_keys = ON;');

        $now = now();

        // ------------------------------------------------------------------
        // PHASE 1: Academic Year & Semesters
        // ------------------------------------------------------------------
        $academicYear = AcademicYear::create([
            'name'       => '2025-2026',
            'start_date' => $now->copy()->startOfYear(),
            'end_date'   => $now->copy()->endOfYear(),
            'is_current' => true,
        ]);

        Semester::insert([
            [
                'academic_year_id' => $academicYear->id,
                'name'             => 'Semester 1',
                'start_date'       => $now->copy()->subMonth(),
                'end_date'         => $now->copy()->addMonths(3),
                'is_active'        => true, // Active Semester
                'created_at'       => $now, 'updated_at' => $now,
            ],
            [
                'academic_year_id' => $academicYear->id,
                'name'             => 'Semester 2',
                'start_date'       => $now->copy()->addMonths(4),
                'end_date'         => $now->copy()->addMonths(8),
                'is_active'        => false,
                'created_at'       => $now, 'updated_at' => $now,
            ],
        ]);

        // Cache the active semester ID for scheduling
        $activeSemesterId = Semester::where('is_active', true)->value('id');

        // ------------------------------------------------------------------
        // PHASE 2: Global Lookups (Degrees)
        // ------------------------------------------------------------------
        Degree::insert([
            ['name' => 'Bachelor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Master', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'PhD', 'created_at' => $now, 'updated_at' => $now],
        ]);
        $degreeMap = Degree::pluck('id', 'name')->toArray();

        // ------------------------------------------------------------------
        // PHASE 3: Hierarchy (Faculty -> Dept -> Major -> Program)
        // ------------------------------------------------------------------
        $structure = $this->getUniversityStructure();

        // A. Faculties
        $facultyData = [];
        foreach (array_keys($structure) as $name) {
            $facultyData[] = ['name' => $name, 'created_at' => $now, 'updated_at' => $now];
        }
        Faculty::insert($facultyData);
        $facultyMap = Faculty::pluck('id', 'name')->toArray();

        // B. Departments
        $deptData  = [];
        $usedCodes = [];

        foreach ($structure as $facName => $depts) {
            $facId = $facultyMap[$facName];
            foreach (array_keys($depts) as $deptName) {
                // Generate Unique Code
                $code         = $this->generateCode($deptName);
                $originalCode = $code;
                $counter      = 1;
                while (in_array($code, $usedCodes)) {
                    $code = substr($originalCode, 0, 4) . $counter;
                    $counter++;
                }
                $usedCodes[] = $code;

                $deptData[] = [
                    'name'       => $deptName,
                    'code'       => $code,
                    'faculty_id' => $facId,
                    'created_at' => $now, 'updated_at' => $now,
                ];
            }
        }
        Department::insert($deptData);
        $deptMap = Department::pluck('id', 'name')->toArray();

        // C. Majors
        $majorData = [];
        foreach ($structure as $facName => $depts) {
            foreach ($depts as $deptName => $majors) {
                $deptId = $deptMap[$deptName] ?? null;
                if (! $deptId) {
                    continue;
                }

                foreach ($majors as $majorName) {
                    $majorData[] = [
                        'name'          => $majorName,
                        'department_id' => $deptId,
                        'degree_id'     => $degreeMap['Bachelor'],
                        'cost_per_term' => 1500.00, // Updated column name per new migration
                        'created_at'    => $now, 'updated_at' => $now,
                    ];
                }
            }
        }
        // Batch Insert Majors (200 at a time)
        foreach (array_chunk($majorData, 200) as $chunk) {
            Major::insert($chunk);
        }
        $majorMap = Major::pluck('id', 'name')->toArray();

        // D. Programs
        $programData = [];
        foreach ($majorMap as $name => $id) {
            $programData[] = [
                'name'       => "Bachelor of $name",
                'major_id'   => $id,
                'degree_id'  => $degreeMap['Bachelor'],
                'created_at' => $now, 'updated_at' => $now,
            ];

            // Random Masters
            if ($id % 2 === 0) {
                $programData[] = [
                    'name'       => "Master of $name",
                    'major_id'   => $id,
                    'degree_id'  => $degreeMap['Master'],
                    'created_at' => $now, 'updated_at' => $now,
                ];
            }
        }
        foreach (array_chunk($programData, 200) as $chunk) {
            Program::insert($chunk);
        }

        // ------------------------------------------------------------------
        // PHASE 4: Staff & Catalog Setup
        // ------------------------------------------------------------------
        // Create default Instructor
        $instructor = User::firstOrCreate(
            ['email' => 'staff@university.com'],
            [
                'name'      => 'Dr. Default Staff',
                'username'  => 'staff',
                'password'  => '$2y$12$K.x.examplehash', // Fast hash
                'is_active' => true,
            ]
        );

        $programs = Program::with('major.department')->get();
        $days     = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
        $times    = ['08:00:00', '10:00:00', '13:00:00', '15:00:00'];

        foreach ($programs as $program) {
            $deptName  = $program->major->department->name;
            $majorName = $program->major->name;
            $deptCode  = $program->major->department->code; // e.g. "CS"

            // We will generate 8 terms worth of data (4 years * 2 semesters)
            // 5 courses per term = 40 courses per program.
            // If you have ~20 programs, that's 800 courses instantly.

            for ($year = 1; $year <= 4; $year++) {
                for ($term = 1; $term <= 2; $term++) {

                    // Create 5 courses per semester
                    for ($c = 1; $c <= 5; $c++) {

                                                                 // Smart Naming Logic
                        $level     = $year * 100;                // 100, 200, 300, 400
                        $courseNum = $level + ($term * 10) + $c; // e.g. 111, 112... 121...
                        $code      = "{$deptCode}{$courseNum}";

                        // Generate a plausible name
                        $suffixes = ['Fundamentals', 'Principles', 'Theory', 'Laboratory', 'Analysis', 'Applications', 'History', 'Ethics', 'Design', 'Management'];
                        $name     = $c === 1 ? "Intro to {$majorName}" : "{$majorName} " . $suffixes[array_rand($suffixes)];
                        if ($year > 2) {
                            $name = "Advanced {$name}";
                        }

                        // 1. Create Course
                        $course = Course::firstOrCreate(
                            ['code' => $code],
                            [
                                'name'          => $name,
                                'department_id' => $program->major->department_id,
                                'credits'       => rand(2, 4),
                                'description'   => "Standard curriculum course for {$majorName}, Year {$year}.",
                            ]
                        );

                        // 2. Schedule Class (Active Semester Only)
                        // To avoid clutter, we only schedule "Year 1" classes in the Active Semester for the demo
                        // Or you can schedule everything if you want massive data.
                        // 2. Schedule Class
                        if ($year === 1) {
                            $startTime = $times[array_rand($times)];
                            // Add 1 hour 30 mins (5400 seconds)
                            $endTime = date('H:i:s', strtotime($startTime) + 5400);

                            ClassSession::updateOrCreate([
                                'course_id'     => $course->id,
                                'semester_id'   => $activeSemesterId,
                                'section_name'  => 'A',
                            ],
                            [
                                'instructor_id' => $instructor->id,
                                'capacity'      => 40,
                                'day_of_week'   => $days[array_rand($days)],
                                'start_time'    => $startTime, // Use variable
                                'end_time'      => $endTime,   // Use calculated time
                                'status'        => 'open',
                            ]);
                        }

                        // 3. Add to Roadmap
                        DB::table('program_structures')->insertOrIgnore([
                            'program_id'       => $program->id,
                            'course_id'        => $course->id,
                            'recommended_year' => $year,
                            'recommended_term' => $term,
                            'created_at'       => $now,
                            'updated_at'       => $now,
                        ]);
                    }
                }
            }
        }
    }

    private function generateCode(string $string): string
    {
        $words = array_filter(preg_split('/[\s\&\-\/\\\\]+/', strtoupper($string)));
        $code  = '';
        foreach ($words as $w) {
            if (in_array($w, ['OF', 'AND'])) {
                continue;
            }

            $code .= substr($w, 0, 1);
        }
        return (strlen($code) < 2) ? strtoupper(substr($string, 0, 3)) : substr($code, 0, 4);
    }
    private function getUniversityStructure()
    {
        return [
            'Faculty of Engineering & Technology' => [
                'Computer Science'       => [
                    'Software Engineering',
                    'Artificial Intelligence',
                    'Cyber Security',
                    'Data Science',
                    'Network Engineering',
                    'Information Systems',
                    'Game Development',
                    'Cloud Computing',
                ],
                'Electrical Engineering' => [
                    'Power Systems',
                    'Electronics & Telecommunication',
                    'Mechatronics',
                    'Control Systems',
                    'Renewable Energy',
                    'Signal Processing',
                ],
                'Civil Engineering'      => [
                    'Structural Engineering',
                    'Geotechnical Engineering',
                    'Transportation Engineering',
                    'Construction Management',
                    'Environmental Engineering',
                    'Urban Planning',
                ],
                'Mechanical Engineering' => [
                    'Automotive Engineering',
                    'Thermal Engineering',
                    'Manufacturing Systems',
                    'Robotics',
                    'Aerospace Engineering',
                    'Fluid Dynamics',
                ],
                'Chemical Engineering'   => [
                    'Process Engineering',
                    'Materials Science',
                    'Biochemical Engineering',
                    'Petroleum Engineering',
                ],
            ],
            'Faculty of Business & Economics'     => [
                'Business Administration' => [
                    'Marketing Management',
                    'Human Resource Management',
                    'Supply Chain Management',
                    'International Business',
                    'Entrepreneurship',
                    'Operations Management',
                    'Strategic Management',
                ],
                'Accounting & Finance'    => [
                    'Public Accounting',
                    'Corporate Finance',
                    'Banking & Insurance',
                    'Taxation',
                    'FinTech',
                    'Risk Management',
                    'Investment Analysis',
                ],
                'Economics'               => [
                    'Development Economics',
                    'International Economics',
                    'Econometrics',
                    'Behavioral Economics',
                    'Public Policy',
                ],
            ],
            'Faculty of Health Sciences'          => [
                'Nursing'       => [
                    'Adult Health Nursing',
                    'Pediatric Nursing',
                    'Critical Care Nursing',
                    'Community Health',
                    'Mental Health Nursing',
                    'Midwifery',
                ],
                'Medicine'      => [
                    'General Medicine',
                    'Surgery',
                    'Pediatrics',
                    'Internal Medicine',
                    'Cardiology',
                    'Neurology',
                ],
                'Pharmacy'      => [
                    'Clinical Pharmacy',
                    'Pharmaceutical Chemistry',
                    'Pharmacology',
                    'Toxicology',
                    'Industrial Pharmacy',
                ],
                'Public Health' => [
                    'Epidemiology',
                    'Health Policy & Management',
                    'Environmental Health',
                    'Nutrition & Dietetics',
                    'Biostatistics',
                ],
                'Dentistry'     => [
                    'Oral Surgery',
                    'Orthodontics',
                    'Periodontology',
                    'Prosthodontics',
                ],
            ],
            'Faculty of Arts & Humanities'        => [
                'English Language & Literature' => [
                    'English Literature',
                    'Linguistics',
                    'Creative Writing',
                    'Teaching English as a Second Language (TESL)',
                    'Comparative Literature',
                ],
                'History & Political Science'   => [
                    'Modern History',
                    'Political Science',
                    'International Relations',
                    'Archaeology',
                    'Ancient History',
                    'Public Administration',
                ],
                'Psychology'                    => [
                    'Clinical Psychology',
                    'Counseling Psychology',
                    'Industrial-Organizational Psychology',
                    'Developmental Psychology',
                    'Forensic Psychology',
                ],
                'Sociology & Anthropology'      => [
                    'Sociology',
                    'Cultural Anthropology',
                    'Social Work',
                    'Criminology',
                ],
                'Philosophy & Religion'         => [
                    'Philosophy',
                    'Ethics',
                    'Religious Studies',
                    'Logic',
                ],
            ],
            'Faculty of Science'                  => [
                'Biological Sciences'            => [
                    'Microbiology',
                    'Genetics',
                    'Biotechnology',
                    'Botany',
                    'Zoology',
                    'Marine Biology',
                    'Bioinformatics',
                ],
                'Chemistry'                      => [
                    'Organic Chemistry',
                    'Analytical Chemistry',
                    'Industrial Chemistry',
                    'Physical Chemistry',
                    'Inorganic Chemistry',
                ],
                'Physics & Mathematics'          => [
                    'Theoretical Physics',
                    'Applied Mathematics',
                    'Statistics',
                    'Astrophysics',
                    'Actuarial Science',
                    'Nuclear Physics',
                ],
                'Earth & Environmental Sciences' => [
                    'Geology',
                    'Environmental Science',
                    'Meteorology',
                    'Oceanography',
                ],
            ],
            'Faculty of Law'                      => [
                'Law' => [
                    'Civil Law',
                    'Criminal Law',
                    'International Law',
                    'Corporate Law',
                    'Constitutional Law',
                    'Human Rights Law',
                ],
            ],
            'Faculty of Architecture & Design'    => [
                'Architecture' => [
                    'Architectural Design',
                    'Landscape Architecture',
                    'Urban Design',
                    'Interior Architecture',
                ],
                'Design'       => [
                    'Graphic Design',
                    'Industrial Design',
                    'Fashion Design',
                    'Multimedia Arts',
                    'Animation',
                ],
            ],
            'Faculty of Education'                => [
                'Education' => [
                    'Primary Education',
                    'Secondary Education',
                    'Educational Leadership',
                    'Special Education',
                    'Curriculum Design',
                ],
            ],
        ];
    }
}
