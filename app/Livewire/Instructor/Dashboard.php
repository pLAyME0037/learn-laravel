<?php

namespace App\Livewire\Instructor;

use App\Models\ClassSession;
use App\Models\Semester;
use Livewire\Component;
use Livewire\Attributes\Layout;

class Dashboard extends Component
{
    public $myClasses = [];
    public $activeSemesterName;

    public function mount()
    {
        $user = auth()->user();
        
        // Ensure user is an instructor (linked via ID or Role)
        // Adjust logic if you use 'instructor_id' column on users or polymorphic
        // Assuming ClassSession.instructor_id links to Users table:
        
        $activeSemester = Semester::where('is_active', true)->first();
        $this->activeSemesterName = $activeSemester ? $activeSemester->name : 'Unknown';

        if ($activeSemester) {
            $this->myClasses = ClassSession::with(['course', 'classroom'])
                ->where('instructor_id', $user->id)
                ->where('semester_id', $activeSemester->id)
                ->get();
        }
    }

    #[Layout('layouts.app', ['header' => 'Instructor Portal'])]
    public function render()
    {
        return view('livewire.instructor.dashboard');
    }
}