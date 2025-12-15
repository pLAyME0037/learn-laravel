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

        // Pre-fetch Program Map for speed
        $programMap = Program::pluck('id', 'name')->toArray();

        // Define Specific Courses to Seed
        $coursesToSeed = [
            // =================================================================
            // BACHELOR OF SOFTWARE ENGINEERING (The Main Demo)
            // =================================================================
            
            // --- Year 1, Term 1 (Freshman - Full Load of 5 Classes) ---
            ['Bachelor of Software Engineering', 'CS101',   'Intro to Programming',       3, 1, 1, 'Logic and Python/C++ basics.'],
            ['Bachelor of Software Engineering', 'MATH101', 'Calculus I',                 3, 1, 1, 'Limits and Derivatives.'],
            ['Bachelor of Software Engineering', 'ENG101',  'Academic English',           3, 1, 1, 'Writing and Research skills.'],
            ['Bachelor of Software Engineering', 'PHY101',  'General Physics I',          3, 1, 1, 'Mechanics and Motion.'],
            ['Bachelor of Software Engineering', 'HIS101',  'World History',              2, 1, 1, 'General Education requirement.'],

            // --- Year 1, Term 2 (Progression) ---
            ['Bachelor of Software Engineering', 'CS102',   'Object Oriented Programming', 4, 1, 2, 'Java/C# concepts.'],
            ['Bachelor of Software Engineering', 'CS103',   'Web Fundamentals',            3, 1, 2, 'HTML, CSS, JS.'],
            ['Bachelor of Software Engineering', 'MATH102', 'Linear Algebra',              3, 1, 2, 'Vectors and Matrices.'],
            ['Bachelor of Software Engineering', 'STAT101', 'Probability & Statistics',    3, 1, 2, 'Data analysis basics.'],
            
            // --- Year 2, Term 1 (Sophomore - Advanced) ---
            ['Bachelor of Software Engineering', 'CS201',   'Data Structures & Algos',     4, 2, 1, 'Trees, Graphs, Sorting.'],
            ['Bachelor of Software Engineering', 'CS202',   'Database Systems',            3, 2, 1, 'SQL and Relational Design.'],

            // =================================================================
            // OTHER MAJORS (To show filtering works)
            // =================================================================
            
            // Mathematics
            ['Bachelor of Applied Mathematics', 'MATH101', 'Calculus I',                 3, 1, 1, 'Shared Course.'],
            ['Bachelor of Applied Mathematics', 'MATH105', 'Discrete Math',              3, 1, 1, 'Logic and Proofs.'],

            // Marketing
            ['Bachelor of Marketing Management', 'MKT101', 'Principles of Marketing',    3, 1, 1, 'Market analysis basics.'],
            ['Bachelor of Marketing Management', 'ECO101', 'Microeconomics',             3, 1, 1, 'Supply and Demand.'],
            ['Bachelor of Marketing Management', 'ACC101', 'Financial Accounting',       3, 1, 1, 'Balance sheets.'],
        ];

        foreach ($coursesToSeed as [$progName, $cCode, $cName, $credits, $year, $termNum, $desc]) {
            $progId = $programMap[$progName] ?? null;
            if (! $progId) {
                continue;
            }

            // Efficiently find Department via Program -> Major
            $program = Program::with('major')->find($progId);

            // 1. Create Course (Catalog)
            $course = Course::firstOrCreate(
                ['code' => $cCode],
                [
                    'name'          => $cName,
                    'department_id' => $program->major->department_id,
                    'credits'       => $credits,
                    'description'   => $desc,
                ]
            );

            // 2. Schedule Class Session (The Instance)
            ClassSession::create([
                'course_id'     => $course->id,
                'semester_id'   => $activeSemesterId,
                'instructor_id' => $instructor->id,
                'section_name'  => 'A', // Fixed: matches schema 'section_name'
                'capacity'      => 40,
                'day_of_week'   => 'Mon',
                'start_time'    => '09:00:00',
                'end_time'      => '10:30:00',
                'status'        => 'open',
            ]);

            // 3. Add to Roadmap (Program Structure)
            // This is critical for the Batch Enrollment tool to work
            DB::table('program_structures')->insertOrIgnore([
                'program_id'       => $progId,
                'course_id'        => $course->id,
                'recommended_year' => $year,
                'recommended_term' => $termNum,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
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
