<?php
namespace App\Livewire\Admin\Academic;

use App\Models\ClassSession;
use App\Models\Department;
use App\Models\Program;
use App\Models\Semester;
use App\Models\Student;
use App\Services\BatchEnrollmentService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

class BatchEnrollment extends Component
{
    // --- Filters ---
    public $department_id = '';
    public $program_id    = '';
    public $semester_id   = '';

    // --- Cohort Selection ---
    public $year_level = 1;
    public $semester   = 1;

    // --- Data ---
    public $availableClasses = [];
    public $selectedClasses  = [];

    public function mount()
    {
        $this->semester_id = Semester::where('is_active', true)->value('id');
    }

    // --- Lifecycle Hooks (Auto-Load when dropdowns change) ---
    public function updatedProgramId()
    {$this->loadRecommendedClasses();}
    public function updatedYearLevel()
    {$this->loadRecommendedClasses();}
    public function updatedTermNumber()
    {$this->loadRecommendedClasses();}
    public function updatedSemesterId()
    {$this->loadRecommendedClasses();}

    /**
     * The Logic Engine:
     * 1. Looks at 'program_structures' to see what courses are required for Year X / Term Y.
     * 2. Looks at 'class_sessions' to find the scheduled instances of those courses.
     */
    public function loadRecommendedClasses()
    {
        // Reset
        $this->availableClasses = [];
        $this->selectedClasses  = [];

        if (! $this->program_id || ! $this->semester_id) {
            return;
        }

        // 1. Get Course IDs from the Roadmap (program_structures)
        $courseIds = DB::table('program_structures')
            ->where('program_id', $this->program_id)
            ->where('recommended_year', $this->year_level)
            ->where('recommended_term', $this->semester)
            ->pluck('course_id');

        if ($courseIds->isEmpty()) {
            return;
        }

        // 2. Find Scheduled Classes for these Courses in the selected Calendar Semester
        $this->availableClasses = ClassSession::with(['course', 'instructor'])
            ->where('semester_id', $this->semester_id)
            ->whereIn('course_id', $courseIds)
            ->where('status', 'open')
            ->get();

        // 3. Auto-Select all found classes by default
        foreach ($this->availableClasses as $class) {
            $this->selectedClasses[] = (string) $class->id;
        }
    }

    /**
     * The trigger (Click Button)
     */
    public function confirmEnrollment()
    {
        $this->validate([
            'program_id'      => 'required',
            'semester_id'     => 'required',
            'selectedClasses' => 'required|array|min:1',
        ]);

        // 1. Calculate the Student Term (1-8)
        // Formula: Year 1/Sem 1 = Term 1. Year 2/Sem 1 = Term 3.
        $targetTerm = ($this->year_level - 1) * 2 + $this->semester;

        $studentCount = Student::query()
            ->where('program_id', $this->program_id)
            ->where('current_term', $targetTerm) // Using the new Integer column
            ->where('academic_status', 'active')
            ->count();

        if ($studentCount === 0) {
            $this->dispatch('swal:error', [
                'message' => 'No active student students found for this batch.',
            ]);
            return;
        }

        $this->dispatch('swal:confirm', [
            'title' => 'Ready to Enroll?',
            'text'  => "You are about to enroll {$studentCount} students into "
            . count($this->selectedClasses) . " classes.",
            'method' => 'runEnrollment',
        ]);
    }

    #[On('runEnrollment')]
    public function runEnrollment(BatchEnrollmentService $service)
    {
        try {
            $targetTerm = ($this->year_level - 1) * 2 + $this->semester;

            $students = Student::query()
                ->where('program_id', $this->program_id)
                ->where('current_term', $targetTerm) // Using the new Integer column
                ->where('academic_status', 'active')
                ->get();

            $classes = ClassSession::whereIn('id', $this->selectedClasses)
                ->get();

            $count = $service->enrollCohort($students, $classes);
            $this->dispatch('swal:success', [
                'message' => "Batch Complete! {$count} enrollment created.",
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'message' => "Batch failed: " . $e->getMessage(),
            ]);
        }
    }

    #[Layout('layouts.app', ['header' => 'Batch Class Enrollment'])]
    public function render()
    {
        // Dropdown Data
        $departments = Department::orderBy('name')->get();

        $programs = Program::query()
            ->when($this->department_id, fn($q) =>
                $q->whereHas('major', fn($m) =>
                    $m->where('department_id', $this->department_id)))
            ->orderBy('name')
            ->get();

        $semesters = Semester::orderByDesc('start_date')->get();

        // Calculate Student Count for UI preview
        $targetTerm = ($this->year_level - 1) * 2 + $this->semester;

        $studentCount = 0;
        if ($this->program_id) {
            $studentCount = Student::where('program_id', $this->program_id)
                ->where('current_term', $targetTerm)
                ->where('academic_status', 'active')
                ->count();
        }

        return view('livewire.admin.academic.batch-enrollment', [
            'departments'  => $departments,
            'programs'     => $programs,
            'semesters'    => $semesters,
            'studentCount' => $studentCount,
        ]);
    }
}
