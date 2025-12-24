<?php
namespace App\Livewire\Academic;

use App\Models\Student;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ScheduleViewer extends Component
{
    public $schedule  = [];
    public $days      = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    public $timeSlots = [];

    public function mount()
    {
        $student = Student::where('user_id', auth()->id())->first();

        if (! $student) {
            return;
        }

        // 1. Fetch Enrolled Classes
        $classes = $student->enrollments()
            ->whereIn('status', ['enrolled', 'completed', 'failed'])
            ->with([
                'classSession.course',
                'classSession.instructor',
                'classSession.classroom',
            ])
            ->get()
            ->pluck('classSession');

        // 2. Organize by Day -> Time
        foreach ($classes as $class) {
            $day = trim($class->day_of_week);
            // Create a sorting key based on start time (e.g., 09:00)
            $this->schedule[$day][] = $class;
        }

        // 3. Sort classes within each day
        foreach ($this->schedule as $day => &$classes) {
            usort($classes, fn($a, $b) => strcmp($a->start_time, $b->start_time));
        }

        // dd(array_keys($this->schedule));
    }

    #[Layout('layouts.app', ['header' => 'My Class Schedule'])]
    public function render()
    {
        return view('livewire.academic.schedule-viewer');
    }
}
