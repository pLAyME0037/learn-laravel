<?php
namespace App\Livewire\Admin\Academic;

use App\Models\Classroom;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\Semester;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class ScheduleManager extends Component
{
    use WithPagination;

    // Filters
    public $semester_id;
    public $filterDay  = '';
    public $search     = '';
    public $filterInst = '';

    // Modal State
    public $showModal = false;
    public $isEditing = false;
    public $sessionId;

    // Form Fields
    public $course_id, $instructor_id, $classroom_id;
    public $section_name = 'A';
    public $day_of_week  = 'Mon', $start_time, $end_time;
    public $capacity     = 40;
    public $status       = 'open';

    public function mount()
    {
        $this->semester_id = Semester::where('is_active', true)->value('id');
    }

    public function updatedSemesterId()
    {$this->resetPage();}
    public function updatedSearch()
    {$this->resetPage();}
    public function updatedFilterInst()
    {$this->resetPage();}
    public function updatedFilterDay()
    {$this->resetPage();}

    public function create()
    {
        $this->reset([
            'sessionId',
            'course_id',
            'instructor_id',
            'classroom_id',
            'start_time',
            'end_time',
            'isEditing',
        ]);
        $this->section_name = 'A';
        $this->capacity     = 40;
        $this->day_of_week  = 'Mon';
        $this->status       = 'open';
        $this->showModal    = true;
    }

    public function edit($id)
    {
        $session = ClassSession::findOrFail($id);

        $this->sessionId = $session->id;
        $this->course_id = $session->course_id;

        // FIX: Ensure we use the ID that matches the Dropdown Value
        // Since the dropdown uses $inst->user_id (User ID), and session stores User ID, this matches.
        // If session stored Instructor ID, we would need $session->instructor->user_id here.
        $this->instructor_id = $session->instructor_id;

        $this->classroom_id = $session->classroom_id;
        $this->section_name = $session->section_name;
        $this->capacity     = $session->capacity;
        $this->day_of_week  = $session->day_of_week;
        $this->start_time   = $session->start_time;
        $this->end_time     = $session->end_time;
        $this->status       = $session->status;

        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'semester_id'   => 'required',
            'course_id'     => 'required|exists:courses,id',
            'instructor_id' => 'required|exists:users,id', // Validating against Users table
            'classroom_id'  => 'required|exists:classrooms,id',
            'section_name'  => 'required|string|max:10',
            'day_of_week'   => 'required',
            'start_time'    => 'required',
            'end_time'      => 'required|after:start_time',
            'capacity'      => 'required|integer|min:1',
        ]);

        $data = [
            'semester_id'   => $this->semester_id,
            'course_id'     => $this->course_id,
            'instructor_id' => $this->instructor_id,
            'classroom_id'  => $this->classroom_id,
            'section_name'  => $this->section_name,
            'day_of_week'   => $this->day_of_week,
            'start_time'    => $this->start_time,
            'end_time'      => $this->end_time,
            'capacity'      => $this->capacity,
            'status'        => $this->status,
        ];

        ClassSession::updateOrCreate(['id' => $this->sessionId], $data);

        $this->showModal = false;
        $this->dispatch('swal:success', ['message' => 'Class session saved.']);
    }

    public function delete($id)
    {
        $session = ClassSession::find($id);
        if ($session && $session->enrollments()->count() > 0) {
            $this->dispatch('swal:error', [
                'message' => 'Cannot delete: Students are enrolled.',
            ]);
            return;
        }
        $session?->delete();
        $this->dispatch('swal:success', ['message' => 'Class deleted.']);
    }

    #[Layout('layouts.app', ['header' => 'Class Schedule'])]
    public function render()
    {
        $sessions = ClassSession::with(['course', 'instructor', 'classroom'])
            ->where('semester_id', $this->semester_id)
            ->when($this->filterDay, fn($q) =>
                $q->where('day_of_week', $this->filterDay))
            ->when($this->filterInst, fn($q) =>
                $q->where('instructor_id', $this->filterInst))
            ->when($this->search, function ($q) {
                $q->whereHas('course', fn($c) =>
                    $c->where('name', 'like', "%{$this->search}%")
                        ->orWhere('code', 'like', "%{$this->search}%")
                );
            })
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->paginate(10);

        // Instructors Dropdown (Mapped to User ID)
        $instructors = Instructor::with(['user', 'department'])
            ->get()
            ->sortBy('user.name')
            ->map(fn($inst) => [
                'id'    => $inst->user_id,
                'label' => $inst->user->name,
                'sub'   => "{$inst->staff_id} • {$inst->department->name}",
            ]);

        // Courses Dropdown
        $courses = Course::orderBy('code')
            ->select('id', 'code', 'name', 'credits')
            ->get()
            ->map(fn($c) => [
                'id'    => $c->id,
                'label' => "{$c->name}",
                'sub'   => "{$c->code} - ({$c->credits} Credits)",
            ]);

        // Classrooms Dropdown
        $classrooms = Classroom::orderBy('building_name')
            ->orderBy('room_number')
            ->get()
            ->map(fn($c) => [
                'id'    => $c->id,
                'label' => "{$c->room_number} ({$c->type})",
                'sub' => "Cap: {$c->capacity} • {$c->building_name}",
            ]);

        return view('livewire.admin.academic.schedule-manager', [
            'sessions'    => $sessions,
            'semesters'   => Semester::orderByDesc('start_date')->get(),
            'courses'     => $courses,
            'instructors' => $instructors,
            'classrooms'  => $classrooms,
            'days'        => [
                'Mon' => 'Monday', 'Tue'   => 'Tuesday', 'Wed' => 'Wednesday',
                'Thu' => 'Thursday', 'Fri' => 'Friday', 'Sat'  => 'Saturday', 'Sun' => 'Sunday',
            ],
        ]);
    }
}
