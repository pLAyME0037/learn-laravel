<?php
namespace Database\Seeders;

use App\Models\Degree;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Major;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first degree to associate with majors
        $degree = Degree::first();
        if (! $degree) {
            $this->command->error('No degrees found. Please seed the degrees table first.');
            return;
        }

        // The Big Data Structure
        // Hierarchy: Faculty Name => [ Department Name => [ List of Majors ] ]
        $structure = [
            'Faculty of Engineering & Technology' => [
                'Computer Science'       => [
                    'Software Engineering',
                    'Artificial Intelligence',
                    'Cyber Security',
                    'Data Science',
                    'Network Engineering',
                    'Information Systems',
                ],
                'Electrical Engineering' => [
                    'Power Systems',
                    'Electronics & Telecommunication',
                    'Mechatronics',
                    'Control Systems',
                ],
                'Civil Engineering'      => [
                    'Structural Engineering',
                    'Geotechnical Engineering',
                    'Transportation Engineering',
                    'Construction Management',
                ],
                'Mechanical Engineering' => [
                    'Automotive Engineering',
                    'Thermal Engineering',
                    'Manufacturing Systems',
                    'Robotics',
                ],
            ],
            'Faculty of Business & Economics'     => [
                'Business Administration' => [
                    'Marketing Management',
                    'Human Resource Management',
                    'Supply Chain Management',
                    'International Business',
                    'Entrepreneurship',
                ],
                'Accounting & Finance'    => [
                    'Public Accounting',
                    'Corporate Finance',
                    'Banking & Insurance',
                    'Taxation',
                    'FinTech',
                ],
                'Economics'               => [
                    'Development Economics',
                    'International Economics',
                    'Econometrics',
                ],
            ],
            'Faculty of Health Sciences'          => [
                'Nursing'       => [
                    'Adult Health Nursing',
                    'Pediatric Nursing',
                    'Critical Care Nursing',
                    'Community Health',
                ],
                'Medicine'      => [
                    'General Medicine',
                    'Surgery',
                    'Pediatrics',
                    'Internal Medicine',
                ],
                'Pharmacy'      => [
                    'Clinical Pharmacy',
                    'Pharmaceutical Chemistry',
                    'Pharmacology',
                ],
                'Public Health' => [
                    'Epidemiology',
                    'Health Policy & Management',
                    'Environmental Health',
                ],
            ],
            'Faculty of Arts & Humanities'        => [
                'English Language & Literature' => [
                    'English Literature',
                    'Linguistics',
                    'Creative Writing',
                    'Teaching English as a Second Language (TESL)',
                ],
                'History & Political Science'   => [
                    'Modern History',
                    'Political Science',
                    'International Relations',
                    'Archaeology',
                ],
                'Psychology'                    => [
                    'Clinical Psychology',
                    'Counseling Psychology',
                    'Industrial-Organizational Psychology',
                ],
            ],
            'Faculty of Science'                  => [
                'Biological Sciences'   => [
                    'Microbiology',
                    'Genetics',
                    'Biotechnology',
                    'Botany',
                    'Zoology',
                ],
                'Chemistry'             => [
                    'Organic Chemistry',
                    'Analytical Chemistry',
                    'Industrial Chemistry',
                ],
                'Physics & Mathematics' => [
                    'Theoretical Physics',
                    'Applied Mathematics',
                    'Statistics',
                    'Astrophysics',
                ],
            ],
            'Faculty of Law'                      => [
                'Law' => [
                    'Civil Law',
                    'Criminal Law',
                    'International Law',
                    'Corporate Law',
                ],
            ],
        ];

        // --- STEP 1: Prepare & Bulk Insert Faculties ---
        $facultyData = [];
        $timestamp   = now();

        foreach (array_keys($structure) as $name) {
            $facultyData[] = [
                'name'       => $name,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        // Insert or Ignore to prevent duplicates if run twice
        Faculty::insertOrIgnore($facultyData);

        // Fetch ID map: ['Faculty Name' => 1, ...]
        $facultyMap = Faculty::pluck('id', 'name')->toArray();

        // --- STEP 2: Prepare & Bulk Insert Departments ---
        $deptData = [];
        foreach ($structure as $facultyName => $departments) {
            $facultyId = $facultyMap[$facultyName];

            foreach (array_keys($departments) as $deptName) {
                $deptData[] = [
                    'name'        => $deptName,
                    'code'        => $this->generateCode($deptName),
                    'faculty_id'  => $facultyId,
                    'description' => "Department of $deptName",
                    'created_at'  => $timestamp,
                    'updated_at'  => $timestamp,
                ];
            }
        }

        Department::insertOrIgnore($deptData);
        $deptMap = Department::pluck('id', 'name')->toArray();

        // --- STEP 3: Prepare & Bulk Insert Majors ---
        $majorData = [];
        foreach ($structure as $facultyName => $departments) {
            foreach ($departments as $deptName => $majors) {
                $deptId = $deptMap[$deptName] ?? null;
                if (! $deptId) {
                    continue;
                }

                foreach ($majors as $majorName) {
                    $majorData[] = [
                        'name'              => $majorName,
                        'department_id'     => $deptId,
                        'degree_id'         => $degree->id,
                        'cost'              => rand(500, 2000),
                        'payment_frequency' => ['term', 'year'][array_rand(['term', 'year'])],
                        'created_at'        => $timestamp,
                        'updated_at'        => $timestamp,
                    ];
                }
            }
        }

        // Chunking major inserts to be safe (e.g., insert 100 at a time)
        foreach (array_chunk($majorData, 100) as $chunk) {
            Major::insertOrIgnore($chunk);
        }
    }
    /**
     * Helper to generate a short uppercase code (e.g., "Computer Science" -> "CS")
     */
    private function generateCode(string $string): string
    {
        $words = array_filter(explode(' ', strtoupper($string)), function ($word) {
            return ! in_array($word, ['OF', 'AND', '&', 'THE', 'IN', 'AS', 'A']);
        });
        $code = '';
        foreach ($words as $word) {
            $code .= substr($word, 0, 1);
        }
    
        if (strlen($code) < 2) {
            return strtoupper(substr(Str::slug($string), 0, 3));
        }
    
        return substr($code, 0, 4);
    }
}

