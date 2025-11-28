<?php
namespace Database\Seeders;

use App\Models\Degree;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\Program;
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

        // Fetch degree and major IDs for easier assignment
        $bachelorDegreeId = Degree::where('name', 'Bachelor')->value('id');
        $masterDegreeId = Degree::where('name', 'Master')->value('id');

        $softwareEngineeringMajorId = Major::where('name', 'Software Engineering')->value('id');
        $artificialIntelligenceMajorId = Major::where('name', 'Artificial Intelligence')->value('id');
        $powerSystemsMajorId = Major::where('name', 'Power Systems')->value('id');
        $marketingManagementMajorId = Major::where('name', 'Marketing Management')->value('id');
        $corporateFinanceMajorId = Major::where('name', 'Corporate Finance')->value('id');
        $adultHealthNursingMajorId = Major::where('name', 'Adult Health Nursing')->value('id');
        $englishLiteratureMajorId = Major::where('name', 'English Literature')->value('id');

        // Define programs with only the fillable attributes: degree_id, major_id, name
        $programsData = [
            [
                'name' => 'Bachelor of Software Engineering',
                'degree_id' => $bachelorDegreeId,
                'major_id' => $softwareEngineeringMajorId,
            ],
            [
                'name' => 'Master of Artificial Intelligence',
                'degree_id' => $masterDegreeId,
                'major_id' => $artificialIntelligenceMajorId,
            ],
            [
                'name' => 'Bachelor of Electrical Engineering',
                'degree_id' => $bachelorDegreeId,
                'major_id' => $powerSystemsMajorId,
            ],
            [
                'name' => 'Bachelor of Business Admin',
                'degree_id' => $bachelorDegreeId,
                'major_id' => $marketingManagementMajorId,
            ],
            [
                'name' => 'Master of Corporate Finance',
                'degree_id' => $masterDegreeId,
                'major_id' => $corporateFinanceMajorId,
            ],
            [
                'name' => 'Bachelor of Nursing',
                'degree_id' => $bachelorDegreeId,
                'major_id' => $adultHealthNursingMajorId,
            ],
            [
                'name' => 'Bachelor of Arts in English Literature',
                'degree_id' => $bachelorDegreeId,
                'major_id' => $englishLiteratureMajorId,
            ],
        ];

        foreach ($programsData as $programData) {
            // Check if required IDs are found before attempting to create
            if (is_null($programData['degree_id'])) {
                $this->command->warn("Degree ID for program '{$programData['name']}' not found. Skipping.");
                continue;
            }
            if (is_null($programData['major_id'])) {
                $this->command->warn("Major ID for program '{$programData['name']}' not found. Skipping.");
                continue;
            }

            Program::updateOrCreate(
                ['name' => $programData['name']], // Use name as unique identifier for updateOrCreate
                $programData
            );
        }
    }
}
