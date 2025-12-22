<?php

namespace App\Livewire\Admin\Academic;

use App\Models\ClassSession;
use App\Models\Course;
use App\Models\Semester;
use App\Models\Instructor; // Use Instructor model directly
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class ScheduleManager extends Component
{
    use WithPagination;

    // Filters
    public $semester_id;
    public $filterDay = '';
    public $search = '';

    // Modal Form State
    public $showModal = false;
    public $isEditing = false;
    public $sessionId;

    // Form Fields
    public $course_id, $instructor_id;
    public $section_name = 'A';
    public $day_of_week = 'Mon', $start_time, $end_time;
    public $capacity = 40;
    public $status = 'open';

    public function mount()
    {
        $active = Semester::where('is_active', true)->first();
        $this->semester_id = $active ? $active->id : null;
    }

    public function updatedSemesterId() { $this->resetPage(); }

    public function create()
    {
        $this->reset(['sessionId', 'course_id', 'instructor_id', 'start_time', 'end_time', 'isEditing']);
        $this->section_name = 'A';
        $this->capacity = 40;
        $this->day_of_week = 'Mon';
        $this->status = 'open';
        $this->showModal = true;
    }

    public function edit($id)
    {
        $session = ClassSession::with('instructor')->find($id);
        $this->sessionId = $session->id;
        $this->course_id = $session->course_id;
        // Map to user_id since that's what ClassSession likely stores, or instructor_id based on your schema
        // Assuming ClassSession.instructor_id -> Users.id
        $this->instructor_id = $session->instructor_id; 
        
        $this->section_name = $session->section_name;
        $this->capacity = $session->capacity;
        $this->day_of_week = $session->day_of_week;
        $this->start_time = $session->start_time;
        $this->end_time = $session->end_time;
        $this->status = $session->status;

        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'semester_id' => 'required',
            'course_id' => 'required|exists:courses,id',
            'instructor_id' => 'required|exists:users,id',
            'section_name' => 'required|string|max:10',
            'day_of_week' => 'required',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'capacity' => 'required|integer|min:1',
        ]);

        $data = [
            'semester_id' => $this->semester_id,
            'course_id' => $this->course_id,
            'instructor_id' => $this->instructor_id,
            'section_name' => $this->section_name,
            'day_of_week' => $this->day_of_week,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'capacity' => $this->capacity,
            'status' => $this->status,
        ];

        ClassSession::updateOrCreate(['id' => $this->sessionId], $data);

        $this->showModal = false;
        session()->flash('success', 'Class session saved.');
    }

    public function delete($id)
    {
        $session = ClassSession::find($id);
        if ($session->enrollments()->count() > 0) {
            $this->dispatch('swal:error', ['message' => 'Cannot delete: Students are enrolled.']);
            return;
        }
        $session->delete();
        session()->flash('success', 'Class deleted.');
    }

    #[Layout('layouts.app', ['header' => 'Class Schedule'])]
    public function render()
    {
        $sessions = ClassSession::with(['course', 'instructor'])
            ->where('semester_id', $this->semester_id)
            ->when($this->filterDay, fn($q) => $q->where('day_of_week', $this->filterDay))
            ->when($this->search, function ($q) {
                $q->whereHas('course', fn($c) => 
                    $c->where('name', 'like', "%$this->search%")
                      ->orWhere('code', 'like', "%$this->search%")
                );
            })
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->paginate(10);

        // Better Instructor List (Only actual Staff)
        // Fetch via Instructor model to ensure we get Staff ID + User Name
        $instructors = Instructor::with('user')
            ->get()
            ->map(function($inst) {
                return [
                    'id' => $inst->user_id, // We link session to User ID
                    'name' => $inst->user->name,
                    'staff_id' => $inst->staff_id
                ];
            });

        // Better Course List
        $courses = Course::orderBy('code')
            ->get()
            ->map(function($c) {
                return [
                    'id' => $c->id,
                    'label' => "{$c->code} - {$c->name} ({$c->credits} Cr)"
                ];
            });

        return view('livewire.admin.academic.schedule-manager', [
            'sessions' => $sessions,
            'semesters' => Semester::orderByDesc('start_date')->get(),
            'courses' => $courses,
            'instructors' => $instructors,
            'days' => [
                'Mon' => 'Monday', 'Tue' => 'Tuesday', 'Wed' => 'Wednesday', 
                'Thu' => 'Thursday', 'Fri' => 'Friday', 'Sat' => 'Saturday', 'Sun' => 'Sunday'
            ]
        ]);
    }
}