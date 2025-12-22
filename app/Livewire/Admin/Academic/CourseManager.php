<?php
namespace App\Livewire\Admin\Academic;

use App\Models\Course;
use App\Models\Department;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class CourseManager extends Component
{
    use WithPagination;

    // Filters
    public $search           = '';
    public $filterDepartment = '';

    // Modal State
    public $showModal = false;
    public $isEditing = false;

    // Form Data
    public $courseId;
    public $name, $code, $credits, $department_id, $description;
    public $prerequisites = []; // Array of Course IDs

    // Reset pagination when searching
    public function updatedSearch()
    {$this->resetPage();}

    public function create()
    {
        $this->reset([
            'courseId',
            'name',
            'code',
            'credits',
            'department_id',
            'description',
            'prerequisites',
            'isEditing',
        ]);
        $this->credits   = 3; // Default
        $this->showModal = true;
    }

    public function edit($id)
    {
        $course = Course::with('prerequisites')->find($id);

        $this->courseId      = $course->id;
        $this->name          = $course->name;
        $this->code          = $course->code;
        $this->credits       = $course->credits;
        $this->department_id = $course->department_id;
        $this->description   = $course->description;
        $this->prerequisites = $course
            ->prerequisites
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name'          => 'required|string|max:255',
            'code'          => ['required', 'string', 'max:20', Rule::unique('courses', 'code')->ignore($this->courseId)],
            'credits'       => 'required|integer|min:0|max:10',
            'department_id' => 'required|exists:departments,id',
            'description'   => 'nullable|string',
            'prerequisites' => 'nullable|array',
        ]);

        $data = [
            'name'          => $this->name,
            'code'          => $this->code,
            'credits'       => $this->credits,
            'department_id' => $this->department_id,
            'description'   => $this->description,
        ];

        if ($this->isEditing) {
            $course = Course::find($this->courseId);
            $course->update($data);
        } else {
            $course = Course::create($data);
        }

        // Sync Prerequisites
        $course->prerequisites()->sync($this->prerequisites);

        $this->showModal = false;
        session()->flash('success', 'Course saved successfully.');
    }

    public function delete($id)
    {
        // Add check if course is used in Enrollments or Program Structures?
        Course::find($id)->delete();
        session()->flash('success', 'Course deleted.');
    }

    #[Layout('layouts.app', ['header' => 'Course Catalog'])]
    public function render()
    {
        $courses = Course::with('department')
            ->when($this->search, fn($q) =>
                $q->where('name', 'like', "%$this->search%")
                    ->orWhere('code', 'like', "%$this->search%")
            )
            ->when($this->filterDepartment, fn($q) =>
                $q->where('department_id', $this->filterDepartment)
            )
            ->orderBy('code')
            ->paginate(20);

        $departments = Department::orderBy('name')->pluck('name', 'id');

        // List for Prerequisite Dropdown (exclude self if editing)
        $allCourses = Course::orderBy('code')->get();
        if ($this->isEditing) {
            $allCourses = $allCourses->reject(fn($c) => $c->id == $this->courseId);
        }

        return view('livewire.admin.academic.course-manager', [
            'courses'     => $courses,
            'departments' => $departments,
            'allCourses'  => $allCourses,
        ]);
    }
}
