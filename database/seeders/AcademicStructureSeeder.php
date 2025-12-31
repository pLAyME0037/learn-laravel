<?php
namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\Degree;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\Program;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcademicStructureSeeder extends Seeder
{
    private array $sectionTimes = [
        'A' => ['start' => '08:00:00', 'end' => '10:30:00'], // Morning
        'B' => ['start' => '14:00:00', 'end' => '17:00:00'], // Afternoon
        'C' => ['start' => '17:30:00', 'end' => '20:30:00'], // Evening
    ];

    private array $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];

    public function run(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF;');

        DB::table('program_structures')->truncate();
        ClassSession::truncate();
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

        DB::transaction(function () use ($now) {
            // 1. Academic Year & Semesters
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
                    'is_active'        => true,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ],
                [
                    'academic_year_id' => $academicYear->id,
                    'name'             => 'Semester 2',
                    'start_date'       => $now->copy()->addMonths(4),
                    'end_date'         => $now->copy()->addMonths(8),
                    'is_active'        => false,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ],
            ]);

            $activeSemesterId = Semester::where('is_active', true)->value('id');

            // 2. Degrees
            Degree::insert([
                ['name' => 'Bachelor', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Master', 'created_at' => $now, 'updated_at' => $now],
            ]);
            $degreeMap = Degree::pluck('id', 'name')->toArray();

            // 3. University Hierarchy (Faculty → Dept → Major → Program)
            $structure = $this->getUniversityStructure();

            $facultyData = [];
            foreach (array_keys($structure) as $name) {
                $facultyData[] = ['name' => $name, 'created_at' => $now, 'updated_at' => $now];
            }
            Faculty::insert($facultyData);
            $facultyMap = Faculty::pluck('id', 'name')->toArray();

            $deptData  = [];
            $usedCodes = [];
            foreach ($structure as $facName => $depts) {
                $facId = $facultyMap[$facName];
                foreach (array_keys($depts) as $deptName) {
                    $code     = $this->generateCode($deptName);
                    $original = $code;
                    $i        = 1;
                    while (in_array($code, $usedCodes)) {
                        $code = substr($original, 0, 3) . $i++;
                    }
                    $usedCodes[] = $code;

                    $deptData[] = [
                        'name'       => $deptName,
                        'code'       => $code,
                        'faculty_id' => $facId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
            Department::insert($deptData);
            $deptMap = Department::pluck('id', 'name')->toArray();

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
                            'cost_per_term' => 1500.00,
                            'created_at'    => $now,
                            'updated_at'    => $now,
                        ];
                    }
                }
            }
            Major::insert($majorData);
            $majorMap = Major::pluck('id', 'name')->toArray();

            $programData = [];
            foreach ($majorMap as $name => $id) {
                $programData[] = [
                    'name'       => "Bachelor of $name",
                    'major_id'   => $id,
                    'degree_id'  => $degreeMap['Bachelor'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            Program::insert($programData);

            // 4. Default Instructor
            $instructor = User::firstOrCreate(
                ['email' => 'staff@university.com'],
                [
                    'name'      => 'Dr. Default Staff',
                    'username'  => 'staff',
                    'password'  => bcrypt('password'),
                    'is_active' => true,
                ]
            );

            // 5. Generate Courses, Roadmap, and Class Sessions
            $programs = Program::with('major.department')->get();

            $courseData    = [];
            $structureData = [];
            $sessionData   = [];
            $courseCodes   = [];
            $usedSlots     = [];

            // Step 1: Create unique courses per department
            foreach ($programs as $program) {
                $deptCode  = $program->major->department->code;
                $majorName = $program->major->name;

                for ($year = 1; $year <= 4; $year++) {
                    for ($term = 1; $term <= 2; $term++) {
                        for ($c = 1; $c <= 5; $c++) {
                            $level = $year * 100;
                            $num   = $level + ($term * 10) + $c;
                            $code  = "{$deptCode}{$num}";

                            if (in_array($code, $courseCodes)) {
                                continue; // Already created
                            }
                            $courseCodes[] = $code;

                            $name    = $this->generateCourseName($majorName, $year, $c);
                            $credits = rand(2, 4);

                            $courseData[] = [
                                'code'        => $code,
                                'name'        => $name,
                                'credits'     => $credits,
                                'description' => "Curriculum course for {$majorName}, Year {$year}, Term {$term}.",
                                'department_id' => $program->major->department_id,
                                'created_at'    => $now,
                                'updated_at'    => $now,
                            ];
                        }
                    }
                }
            }

            // Insert courses in chunks
            foreach (array_chunk($courseData, 200) as $chunk) {
                DB::table('courses')->insertOrIgnore($chunk);
            }
            $courseMap = Course::pluck('id', 'code')->toArray();

            // Step 2: Build program roadmap (each program gets its department's courses)
            foreach ($programs as $program) {
                $deptCode = $program->major->department->code;

                for ($year = 1; $year <= 4; $year++) {
                    for ($term = 1; $term <= 2; $term++) {
                        for ($c = 1; $c <= 5; $c++) {
                            $level = $year * 100;
                            $num   = $level + ($term * 10) + $c;
                            $code  = "{$deptCode}{$num}";

                            $courseId = $courseMap[$code] ?? null;
                            if (! $courseId) {
                                continue;
                            }

                            $structureData[] = [
                                'program_id'       => $program->id,
                                'course_id'        => $courseId,
                                'recommended_year' => $year,
                                'recommended_term' => $term,
                                'created_at'       => $now,
                                'updated_at'       => $now,
                            ];
                        }
                    }
                }
            }

            // Insert roadmap in safe chunks
            foreach (array_chunk($structureData, 100) as $chunk) {
                DB::table('program_structures')->insertOrIgnore($chunk);
            }

            // Step 3: Create class sessions (realistic sections)
            foreach ($courseMap as $code => $courseId) {
                preg_match('/(\d{3})(\d)/', $code, $matches);
                $year = (int) ($matches[1] ?? 100) / 100;

                $sections = $year === 1 ? ['A', 'B', 'C'] : ['A']; // Year 1 shared, others per-program

                foreach ($sections as $section) {
                    $time     = $this->sectionTimes[$section];
                    $dayIndex = ($courseId + array_search($section, ['A', 'B', 'C'])) % 5;
                    $day      = $this->days[$dayIndex];

                    $slotKey = "$courseId-$day-{$time['start']}";

                    if (isset($usedSlots[$slotKey])) {
                        continue;
                    }

                    $usedSlots[$slotKey] = true;

                    $sessionData[] = [
                        'course_id'     => $courseId,
                        'semester_id'   => $activeSemesterId,
                        'instructor_id' => $instructor->id,
                        'section_name'  => $section,
                        'capacity'      => 40,
                        'day_of_week'   => $day,
                        'start_time'    => $time['start'],
                        'end_time'      => $time['end'],
                        'status'        => 'open',
                        'created_at'    => $now,
                        'updated_at'    => $now,
                    ];
                }
            }

            // Insert sessions safely
            foreach (array_chunk($sessionData, 100) as $chunk) {
                DB::table('class_sessions')->insertOrIgnore($chunk);
            }
        });
    }

    private function generateCourseName(string $major, int $year, int $index): string
    {
        $bases = [
            "Introduction to $major",
            "$major Fundamentals",
            "$major Principles",
            "Data Structures in $major",
            "$major Systems",
            "$major Architecture",
            "$major Algorithms",
            "$major Development",
            "$major Project",
            "$major Seminar",
        ];

        $name = $bases[($index - 1) % count($bases)];

        if ($year == 2) {
            return "Intermediate $name";
        }

        if ($year >= 3) {
            return "Advanced $name" . ($index % 3 == 0 ? " II" : "");
        }

        return $name;
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

    // Helper to reverse-engineer code from program/year/term (used for mapping)
    private function extractCodeFromProgramAndYearTerm(int $programId, int $year, int $term): ?string
    {
        $program = Program::with('major.department')->find($programId);
        if (! $program) {
            return null;
        }

        $deptCode = $program->major->department->code;
        $level    = $year * 100;
        $num      = $level + ($term * 10) + 1; // approximate

        return "{$deptCode}{$num}";
    }
}
