<?php
namespace App\Livewire\Academic;

use App\Models\Semester;
use App\Models\Student; // If you have this model for financial check
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Dashboard extends Component
{
    public $student;
    public $todaysClasses    = [];
    public $quickLinks       = [];
    public $activities       = [];
    public $semesterProgress = 0;

    public function mount()
    {
        // 1. Fetch Student
        $this->student = Student::with(['program.major.department', 'user'])
            ->where('user_id', auth()->id())
            ->first();

        if (! $this->student) {
            return;
        }

        // 2. Fetch Today's Classes
        $today = Carbon::now()->format('D');

        $this->todaysClasses = $this->student->enrollments()
            ->whereHas('classSession', fn($q) => $q->where('day_of_week', $today))
            ->with(['classSession.course', 'classSession.instructor', 'classSession.classroom'])
            ->get();

        // 3. Calculate Semester Progress
        $activeSemester = Semester::where('is_active', true)->first();
        if ($activeSemester) {
            $start = Carbon::parse($activeSemester->start_date);
            $end   = Carbon::parse($activeSemester->end_date);
            $now   = Carbon::now();

            if ($now->gt($end)) {
                $this->semesterProgress = 100;
            } elseif ($now->lt($start)) {
                $this->semesterProgress = 0;
            } else {
                $totalDays              = $start->diffInDays($end);
                $daysPassed             = $start->diffInDays($now);
                $this->semesterProgress = round(($daysPassed / max(1, $totalDays)) * 100);
            }
        }

        // 4. Quick Links (Static for now, could be dynamic)
        $this->quickLinks = [
            ['title' => 'My Transcript', 'url' => '#', 'icon' => 'document-text'], // Link to Phase E route
            ['title' => 'Financials', 'url' => '/academic/finance', 'icon' => 'currency-dollar'],  // Link to Phase D route
            ['title' => 'Library', 'url' => '#', 'icon' => 'book-open'],
            ['title' => 'Exam Schedule', 'url' => '#', 'icon' => 'calendar'],
            ['title' => 'Weekly Schedule', 'url' => '/academic/schedule', 'icon' => 'calendar'],
        ];

        // 5. Activity Feed (Mockup - Real logic would query 'AuditLogs' or 'Notifications')
        $this->activities = [
            ['title' => 'Semester Started', 'desc' => 'Spring 2026 began', 'date' => '2 days ago', 'type' => 'info'],
            ['title' => 'Enrollment Confirmed', 'desc' => 'Registered for 5 classes', 'date' => '1 week ago', 'type' => 'success'],
        ];

        if ($this->student->has_outstanding_balance) {
            array_unshift($this->activities, [
                'title' => 'Payment Due',
                'desc'  => 'Please clear your balance.',
                'date'  => 'Today',
                'type'  => 'warning',
            ]);
        }
    }

    #[Layout('layouts.app', ['header' => 'Academic Dashboard'])]
    public function render()
    {
        return view('livewire.academic.dashboard');
    }
}
