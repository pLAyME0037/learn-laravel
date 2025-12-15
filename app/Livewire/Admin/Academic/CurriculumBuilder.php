<?php
namespace App\Livewire\Admin\Academic;

use App\Models\Course;
use App\Models\Program;
use App\Models\ProgramStructure;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CurriculumBuilder extends Component
{
    public Program $program;

    // View State
    public $showAddModal = false;

    // Form Data
    public $course_id;
    public $year = 1;
    public $term = 1;

    // Data Sources
    public $allCourses = [];

    public function mount(Program $program)
    {
        $this->program = $program;
        $this->loadCourses();
    }

    public function loadCourses()
    {
        // Exclude courses already in the curriculum
        $usedIds          = $this->program->programStructures()->pluck('course_id');
        $this->allCourses = Course::whereNotIn('id', $usedIds)->orderBy('code')->get();
    }

    public function addCourse()
    {
        $this->validate([
            'course_id' => [
                'required',
                'exists:courses,id',
                Rule::unique('program_structures', 'course_id')
                    ->where('program_id', $this->program->id),
            ],
            'year'      => 'required|integer|min:1|max:6',
            'term'      => 'required|integer|min:1|max:3',
        ],
            [
                'course_id.unique' => 'This course is already in the curriculum.',
            ]
        );

        ProgramStructure::create([
            'program_id'       => $this->program->id,
            'course_id'        => $this->course_id,
            'recommended_year' => $this->year,
            'recommended_term' => $this->term,
        ]);

        $this->showAddModal = false;
        $this->reset(['course_id']);
        $this->loadCourses(); // Refresh dropdown
    }

    public function removeCourse($structureId)
    {
        ProgramStructure::find($structureId)->delete();
        $this->loadCourses(); // Add back to dropdown
    }

    #[Layout('layouts.app', ['header' => 'Curriculum Builder'])]
    public function render()
    {
        // Group roadmap by Year then Term
        $roadmap = ProgramStructure::with('course')
            ->where('program_id', $this->program->id)
            ->orderBy('recommended_year')
            ->orderBy('recommended_term')
            ->get()
            ->groupBy('recommended_year');

        return view('livewire.admin.academic.curriculum-builder', [
            'roadmap' => $roadmap,
        ]);
    }
}
