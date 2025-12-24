<?php
namespace App\Livewire\Instructor;

use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\Dictionary;
use Livewire\Attributes\Layout;
use Livewire\Component;

class AttendanceTaker extends Component
{
    public $classSession;
    public $date;

    // Form Data: [enrollment_id => status]
    public $attendance    = [];
    public $statusOptions = [];

    public function mount($classSessionId)
    {
        $this->classSession = ClassSession::with('enrollments.student.user')
            ->findOrFail($classSessionId);

        // Security Check
        if ($this->classSession->instructor_id !== auth()->id()) {
            abort(403);
        }

        $this->date          = now()->format('Y-m-d');
        $this->statusOptions = Dictionary::options('attendance_status');

        $this->loadAttendance();
    }

    public function updatedDate()
    {
        $this->loadAttendance();
    }

    public function loadAttendance()
    {
        // 1. Fetch existing records for this date
        $existing = Attendance::query()
            ->whereIn('enrollment_id', $this->classSession->enrollments->pluck('id'))
            ->where('date', $this->date)
            ->pluck('status', 'enrollment_id');

        // 2. Initialize form state
        foreach ($this->classSession->enrollments as $enrollment) {
            // Default to 'present' if no record exists
            $this->attendance[$enrollment->id] = $existing[$enrollment->id] ?? 'present';
        }
    }

    public function save()
    {
        foreach ($this->attendance as $enrollmentId => $status) {
            Attendance::updateOrCreate(
                [
                    'enrollment_id' => $enrollmentId,
                    'date'          => $this->date,
                ],
                [
                    'status' => $status,
                ]
            );
        }

        $this->dispatch('swal:success', [
            'message' => 'Attendance saved successfully.',
        ]);
    }

    #[Layout('layouts.app', ['header' => 'Attendance'])]
    public function render()
    {
        return view('livewire.instructor.attendance-taker');
    }
}
