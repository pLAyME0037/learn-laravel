<?php
namespace App\Livewire\Admin\Academic;

use App\Models\ClassSession;
use App\Models\Course;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Semester;
use App\Models\Student;
use App\Services\BatchEnrollmentService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

use function PHPUnit\Framework\isEmpty;

class BatchEnrollment extends Component
{
    public $missingCourses   = [];
    public $availableClasses = [];
    public $selectedClasses  = [];

    public $semester_id   = '';
    public $department_id = '';
    public $program_id    = '';

    public $year_level  = 1;
    public $term_number = 1;

    public $programCounts = [];
    public $cohortCounts  = [];

    public $analysis          = null;
    public $showAnalysisModal = false;

    public function mount()
    {
        $this->semester_id = Semester::where('is_active', true)->value('id');
        $this->calculateCounts();
    }

    public function updatedDepartmentId()
    {
        $this->program_id = '';
        $this->calculateCounts();
    }

    public function updatedProgramId()
    {
        $this->calculateCounts();
        $this->loadRecommendedClasses();
    }

    public function updated($property) {
        $filters = ['year_level', 'term_number', 'semester_id'];
        if(in_array($property, $filters)) {
            $this->loadRecommendedClasses();
        }
    }

    /**
     * Calculate student counts for dropdowns to show context
     */
    public function calculateCounts()
    {
        // 1. Program Counts (Existing)
        $query = Student::select('program_id', DB::raw('count(*) as total'))
            ->where('academic_status', 'active')
            ->groupBy('program_id');

        if ($this->department_id) {
            $query->whereHas(
                'program.major',
                fn($q) => $q->where('department_id', $this->department_id)
            );
        }
        $this->programCounts = $query->pluck('total', 'program_id')->toArray();

        // 2. Cohort Counts (NEW: Breakdown by Term for the selected Program)
        $this->cohortCounts = [];
        if ($this->program_id) {
            $this->cohortCounts = Student::query()
            ->select('current_term', DB::raw('count(*) as total'))
            ->where('program_id', $this->program_id)
            ->where('academic_status', 'active')
            ->groupBy('current_term')
            ->pluck('total', 'current_term')
            ->toArray();
        }
    }

    /**
     * Main Logic: Find classes based on Roadmap + Schedule
     */
    public function loadRecommendedClasses()
    {
        $this->availableClasses = [];
        $this->selectedClasses  = [];
        $this->missingCourses   = [];

        if (! $this->program_id || ! $this->semester_id) {
            return;
        }

        $requiredCourseIds = DB::table('program_structures')
            ->where('program_id', $this->program_id)
            ->where('recommended_year', $this->year_level)
            ->where('recommended_term', $this->term_number)
            ->pluck('course_id');

        if ($requiredCourseIds->isEmpty()) {
            return;
        }

        $this->availableClasses = ClassSession::with(['course', 'user'])
            ->where('semester_id', $this->semester_id)
            ->whereIn('course_id', $requiredCourseIds)
            ->where('status', 'open')
            ->get();

        $foundCourseIds = $this->availableClasses->pluck('course_id')->toArray();
        $missingIds     = $requiredCourseIds->diff($foundCourseIds);
        if ($missingIds->isNotEmpty()) {
            $this->missingCourses = Course::whereIn('id', $missingIds)->get();
        }

        foreach ($this->availableClasses as $class) {
            $this->selectedClasses[] = (string) $class->id;
        }
    }

    public function analyzeEnrollment(BatchEnrollmentService $service)
    {
        $this->validate([
            'program_id'      => 'required',
            'semester_id'     => 'required',
            'selectedClasses' => 'required|array|min:1',
        ]);

        $targetTerm = ($this->year_level - 1) * 2 + $this->term_number;
        $students   = Student::where('program_id', $this->program_id)
            ->where('current_term', $targetTerm)
            ->where('academic_status', 'active')
            ->get();

        $classes = ClassSession::whereIn('id', $this->selectedClasses)->get();

        if ($students === isEmpty()) {
            $this->dispatch('swal:error', [
                'message' => 'No active students found in this block.',
            ]);
            return;
        }

        $this->analysis          = $service->previewChanges($students, $classes);
        $this->showAnalysisModal = true;
    }

    #[On('runRollback')]
    public function runRollback(BatchEnrollmentService $service)
    {
        try {
            $targetTerm = ($this->year_level - 1) * 2 + $this->term_number;

            $count = $service->rollbackCohort($this->program_id, $targetTerm, $this->semester_id);

            $this->dispatch('swal:success', [
                'message' => "Rollback complete. {$count} rollback remove.",
            ]);
            $this->loadRecommendedClasses();
        }
        catch(\Exception $e) {
            $this->dispatch('swal:error', [
                'message' => 'Rollback failed: ' . $e->getMessage()
            ]);
        }
   }

    public function confirmRollback()
    {
        $this->dispatch('swal:confirm', [
            'title' => '⚠️ Danger Rollback Schhdule!',
            'text'  => ""
                . "This will DELETE all enrollments for Year {$this->year_level} "
                . "Sem {$this->term_number} students in this semester. "
                . "Grades will be lost!",
            'icon'               => 'warning',
            'confirmButtonText'  => 'Yes, Delete All',
            'confirmButtonColor' => '#ef4444',
            'method'             => 'runRollback',
        ]);

    }

    public function runEnrollment(BatchEnrollmentService $service)
    {
        try {
            $targetTermStart = ($this->year_level - 1) * 2 + $this->term_number;
            $students        = Student::where('program_id', $this->program_id)
                ->where('current_term', $targetTermStart)
                ->where('academic_status', 'active')
                ->get();

            $classes = ClassSession::whereIn('id', $this->selectedClasses)
                ->withCount(['enrollments as enrolled_count' => fn ($q) =>
                    $q->whereIn('status', ['enrolled', 'completed', 'failed'])
                ])
                ->get();
            foreach ($classes as $class) {
                $inClassStu = Enrollment::where('class_session_id', $class->id)
                ->whereIn('student_id', $students->pluck('id'))
                ->count();
                $newStud = $students->count() - $inClassStu;
                if ($class->enrolled_count + $newStud > $class->capacity) {
                    $i = $newStud; //debug
                    $j = $class->enrolled_count; //debug
                    throw new \Exception(
                        "Class {$class->course->code} is over session capacity "
                        . "of {$class->capacity} < {$i}+{$j}"
                    );
                }
            }

            $count = $service->enrollCohort($students, $classes);
            $this->showAnalysisModal = false;

            $this->dispatch('swal:success', [
                'message' => "Success! {$count} records created.",
            ]);

        } catch (\Exception $e) {
            $this->dispatch('swal:error', ['message' => $e->getMessage()]);
        }
    }

    #[Layout('layouts.app', ['header' => 'Batch Class Enrollment'])]
    public function render()
    {
        $semesters = Semester::with('academicYear')
            ->orderByDesc('start_date')
            ->get()
            ->map(function ($sem) {
                return [
                    'id'    => $sem->id,
                    'label' => "{$sem->academicYear->name} - {$sem->name} "
                    . ($sem->is_active ? '(Active)' : ''),
                ];
            });

        $departments = Department::orderBy('name')->get();

        $programs = Program::query()
            ->when($this->department_id, fn($q) =>
                $q->whereHas('major', fn($q) =>
                    $q->where('department_id', $this->department_id)
                )
            )->orderBy('name')->get();

        $targetTermStart    = ($this->year_level - 1) * 2 + $this->term_number;
        $activeStudentCount = 0;
        $selectedProgram    = null;
        if ($this->program_id) {
            $activeStudentCount = Student::where('program_id', $this->program_id)
                ->where('current_term', $targetTermStart)
                ->where('academic_status', 'active')
                ->count();
            $selectedProgram = Program::find($this->program_id);
        }

        return view('livewire.admin.academic.batch-enrollment', [
            'semesters'          => $semesters,
            'departments'        => $departments,
            'programs'           => $programs,
            'targetStudentCount' => $activeStudentCount,
            'program'            => $selectedProgram,
        ]);
    }
}
