<?php
namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\Semester;
use App\Models\User; // Instructor
use Illuminate\Database\Seeder;

class ClassSessionSeeder extends Seeder
{
    public function run(): void
    {
        $semester = Semester::where('is_active', true)->first();
        if (! $semester) {
            return;
        }

        $courses     = Course::inRandomOrder()->limit(10)->get();
        $instructors = User::role('staff')->inRandomOrder()->limit(5)->get();
        $classrooms  = Classroom::inRandomOrder()->limit(10)->get();

        if ($courses->isEmpty() || $instructors->isEmpty()) {
            return;
        }

        foreach ($courses as $course) {
            ClassSession::create([
                'course_id'     => $course->id,
                'semester_id'   => $semester->id,
                'instructor_id' => $instructors->random()->id,
                'classroom_id'  => $classrooms->random()->id,
                'section_name'  => 'B', // Extra section
                'capacity'      => 40,
                'day_of_week'   => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'][rand(0, 4)],
                'start_time'    => '14:00:00',
                'end_time'      => '15:30:00',
                'status'        => 'open',
            ]);
        }
    }
}
