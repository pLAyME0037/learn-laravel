<?php
namespace App\Livewire\Academic;

use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public $student;
    public $quickLinks       = [];
    public $activities       = [];
    public $semesterProgress = 0;

    public function mount()
    {
        // 1. Get Current Student (Assuming logged in user has student record)
        // Adjust logic if you are an Admin viewing a specific student
        $this->student = Student::with(['department', 'program', 'user'])
            ->where('user_id', Auth::id())
            ->first();

        // 2. Mock Data for the Demo (Replace with DB queries later)
        $this->quickLinks = [
            ['title' => 'University Library', 'url' => '#', 'icon' => 'book-open'],
            ['title' => 'E-Learning (LMS)', 'url' => '#', 'icon' => 'computer-desktop'],
            ['title' => 'Exam Schedule', 'url' => '#', 'icon' => 'calendar'],
            ['title' => 'Student Email', 'url' => '#', 'icon' => 'envelope'],
            ['title' => 'IT Support', 'url' => '#', 'icon' => 'wrench'],
        ];

        $this->activities = [
            ['title' => 'Assignment Submitted', 'desc' => 'Database Systems II', 'date' => '2 mins ago', 'type' => 'success'],
            ['title' => 'Grade Posted', 'desc' => 'Web Development', 'date' => '1 day ago', 'type' => 'info'],
            ['title' => 'Tuition Due', 'desc' => 'Semester 2 Payment', 'date' => '3 days ago', 'type' => 'warning'],
            ['title' => 'Book Returned', 'desc' => 'Introduction to AI', 'date' => '1 week ago', 'type' => 'neutral'],
        ];

        // 3. Calculate Semester Progress (Mock)
        $start = \Carbon\Carbon::parse('2024-01-01');
        $end   = \Carbon\Carbon::parse('2024-06-01');
        $now   = \Carbon\Carbon::now();

        $totalDays  = $start->diffInDays($end);
        $daysPassed = $start->diffInDays($now);

        $this->semesterProgress = min(100, max(0, round(($daysPassed / $totalDays) * 100)));
    }

    public function render()
    {
        return view('livewire.academic.dashboard');
    }
}
