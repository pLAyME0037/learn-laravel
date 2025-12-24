<?php
namespace App\Console\Commands;

use App\Models\Student;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PromoteStudents extends Command
{
    protected $signature   = 'academic:promote-students {--program_id= : Optional filter}';
    protected $description = 'Promote students to the next term/year level.';

    public function handle()
    {
        $this->info("Starting Promotion Process...");

        $query = Student::where('academic_status', 'active');

        if ($this->option('program_id')) {
            $query->where('program_id', $this->option('program_id'));
        }

        $students = $query->get();
        $count    = 0;

        DB::transaction(function () use ($students, &$count) {
            foreach ($students as $student) {
                // 1. Increment Term
                $newTerm = $student->current_term + 1;

                // 2. Calculate New Year Level
                // Term 1,2 => Year 1
                // Term 3,4 => Year 2
                $newYear = ceil($newTerm / 2);

                // 3. Stop at Graduation (e.g. Term 9)
                if ($newTerm > 8) {
                    $student->update(['academic_status' => 'graduated']);
                    $this->line("Student {$student->student_id} marked as Graduated.");
                    continue;
                }

                $student->update([
                    'current_term' => $newTerm,
                    'year_level'   => $newYear,
                ]);

                $count++;
            }
        });

        $this->info("Successfully promoted {$count} students.");
    }
}
