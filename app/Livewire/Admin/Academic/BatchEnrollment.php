<?php
namespace App\Livewire\Admin\Academic;

use App\Models\ClassSession;
use App\Models\Course;
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
    // Filters
    public $missingCourses = [];

    public $semester_id    = ''; // Represents Calendar (Year + Sem)
    public $department_id  = '';
    public $program_id     = '';

    // Batch Selection
    public $year_level  = 1;
    // public $term_number = 1;

    // Data
    public $availableClasses = [];
    public $selectedClasses  = [];

    // Counts (For Dropdowns)
    public $programCounts = [];
    public $cohortCounts  = []; // Stores counts for Year/Term combos

    public function mount()
    {
        // Default to active semester
        $this->semester_id = Semester::where('is_active', true)->value('id');
        $this->calculateCounts();
    }

    public function updatedDepartmentId()
    {
        $this->program_id = ''; // Reset Program if Dept changes
        $this->calculateCounts();
    }

    public function updatedProgramId()
    {
        $this->calculateCounts();
        $this->loadRecommendedClasses();
    }
    public function updatedYearLevel()
    {
        $this->loadRecommendedClasses();
    }
    // public function updatedTermNumber()
    // {
    //     $this->loadRecommendedClasses();
    // }
    public function updatedSemesterId()
    {
        $this->loadRecommendedClasses();
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
            $query->whereHas('program.major', fn($q) =>
                $q->where('department_id', $this->department_id)
            );
        }
        $this->programCounts = $query->pluck('total', 'program_id')->toArray();

        // 2. Cohort Counts (NEW: Breakdown by Term for the selected Program)
        $this->cohortCounts = [];
        if ($this->program_id) {
            $this->cohortCounts = Student::select('current_term', DB::raw('count(*) as total'))
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
        $this->missingCourses = [];

        if (! $this->program_id || ! $this->semester_id) {
            return;
        }

        $courseIdsByTerm = [];
        foreach ([1, 2] as $term) {
            $courseIds = DB::table('program_structures')
                ->where('program_id', $this->program_id)
                ->where('recommended_year', $this->year_level)
                ->where('recommended_term', $term)
                ->pluck('course_id');

            if ($courseIds->isEmpty()) {
                $this->availableClasses[$term] = collect();
                continue;
            }

            $classes = ClassSession::with(['course', 'instructor'])
                ->where('semester_id', $this->semester_id)
                ->whereIn('course_id', $courseIds)  // Only courses in THIS program's roadmap
                ->where('status', 'open')
                ->get();

            $this->availableClasses[$term] = $classes;

            // Missing courses for this term
            $foundIds = $classes->pluck('course_id');
            $missingIds = $courseIds->diff($foundIds);
            $this->missingCourses[$term] = $missingIds->isNotEmpty()
                ? Course::whereIn('id', $missingIds)->get()
                : collect();
        }

        $allCourseIds = collect($courseIdsByTerm)->flatten()->unique();
        if ($allCourseIds->isEmpty()) {
            return;
        }

        $classes = ClassSession::with(['course', 'instructor'])
            ->where('semester_id', $this->semester_id)
            ->whereIn('course_id', $courseIds)
            ->where('status', 'open')
            ->get();

        $this->availableClasses = [];
        foreach([1, 2] as $term) {
            $termCourseIds = $courseIdsByTerm[$term] ?? collect();
            $this->availableClasses[$term] = $classes->whereIn('course_id', $termCourseIds);
        }
        foreach([1, 2] as $term) {
            $foundCourseIds = $this->availableClasses[$term]
                ->pluck('course_id')
                ->toArray();
            $missingIds = $courseIdsByTerm[$term]->diff($foundCourseIds);
            if($missingIds->isNotEmpty()) {
                $this->missingCourses[$term] = Course::whereIn('id', $missingIds)->get();
            }
        }
    }

    public function confirmEnrollment()
    {
        $this->validate([
            'program_id'      => 'required',
            'semester_id'     => 'required',
            'selectedClasses' => 'required|array|min:1',
        ]);

        $targetTerm = ($this->year_level - 1) * 2 + 1;

        $studentCount = Student::where('program_id', $this->program_id)
            ->whereBetween('current_term', [$targetTerm, $targetTerm + 1])
            ->where('academic_status', 'active')
            ->count();

        if ($studentCount === 0) {
            $this->dispatch('swal:error', [
                'message' => 'No active students found in this block.',
            ]);
            return;
        }

        $this->dispatch('swal:confirm', [
            'title'  => 'Confirm Enrollment',
            'text'   => "Enroll {$studentCount} students into " 
                        . count($this->selectedClasses) 
                        . " classes?",
            'method' => 'runEnrollment',
        ]);
    }

    #[On('runEnrollment')]
    public function runEnrollment(BatchEnrollmentService $service)
    {
        try {
            $targetTermStart = ($this->year_level - 1) * 2 + 1;
            $students = Student::where('program_id', $this->program_id)
                ->whereBetween('current_term', [$targetTermStart, $targetTermStart + 1])
                ->where('academic_status', 'active')
                ->get();

            $classes = ClassSession::whereIn('id', $this->selectedClasses)
                ->withCount(['enrollments as enrolled_count' => function ($query) {
                    $query->whereIn('status', ['enrolled', 'completed', 'failed']);
                }])
                ->get();
            foreach($classes as $class) {
                if($class->enrolled_count + $students->count() > $class->capacity) {
                    throw new \Exception(
                        "Class {$class->course->code} is over capacity."
                    );
                }
            }

            $count = $service->enrollCohort($students, $classes);
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

        $programsQuery = Program::query();
        if ($this->department_id) {
            $programsQuery->whereHas('major', fn($q) =>
                $q->where('department_id', $this->department_id)
            );
        }
        $programs = $programsQuery->orderBy('name')->get();

        $activeStudentCount = 0;
        $selectedProgram    = null;
        if ($this->program_id) {
            $targetTermStart = ($this->year_level - 1) * 2 + 1;
            $activeStudentCount = Student::where('program_id', $this->program_id)
            ->whereBetween('current_term', [$targetTermStart, $targetTermStart + 1])
            ->where('academic_status', 'active')
            ->count();
            $selectedProgram = Program::firstWhere('id', $this->program_id);
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
