<?php
namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CoursePrerequisiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch courses by code to establish prerequisites
        $courseCS101   = Course::where('code', 'CS101')->first();
        $courseCS201   = Course::where('code', 'CS201')->first();
        $courseCS501   = Course::where('code', 'CS501')->first();
        $courseMATH101 = Course::where('code', 'MATH101')->first();
        $courseMATH201 = Course::where('code', 'MATH201')->first();
        $courseEE201   = Course::where('code', 'EE201')->first();
        $courseEE301   = Course::where('code', 'EE301')->first();
        $courseMGMT101 = Course::where('code', 'MGMT101')->first();

        // --- Establishing Prerequisites ---

        // CS101 has no prerequisites
        // CS201 requires CS101
        if ($courseCS201 && $courseCS101) {
            $courseCS201->addPrerequisite($courseCS101);
        } else {
            $this->command->warn("Could not establish prerequisite: CS201 requires CS101. One or both courses not found.");
        }

        // CS501 requires CS201
        if ($courseCS501 && $courseCS201) {
            $courseCS501->addPrerequisite($courseCS201);
        } else {
            $this->command->warn("Could not establish prerequisite: CS501 requires CS201. One or both courses not found.");
        }

        // MATH101 has no prerequisites
        // MATH201 requires MATH101
        if ($courseMATH201 && $courseMATH101) {
            $courseMATH201->addPrerequisite($courseMATH101);
        } else {
            $this->command->warn("Could not establish prerequisite: MATH201 requires MATH101. One or both courses not found.");
        }

        // EE201 requires MATH101
        if ($courseEE201 && $courseMATH101) {
            $courseEE201->addPrerequisite($courseMATH101);
        } else {
            $this->command->warn("Could not establish prerequisite: EE201 requires MATH101. One or both courses not found.");
        }

        // EE301 requires EE201 and MATH201
        if ($courseEE301 && $courseEE201) {
            $courseEE301->addPrerequisite($courseEE201);
        } else {
            $this->command->warn("Could not establish prerequisite: EE301 requires EE201. One or both courses not found.");
        }
        if ($courseEE301 && $courseMATH201) {
            $courseEE301->addPrerequisite($courseMATH201);
        } else {
            $this->command->warn("Could not establish prerequisite: EE301 requires MATH201. One or both courses not found.");
        }

        // MGMT101 has no prerequisites
        // Add more prerequisites as needed for other courses.
    }
}
