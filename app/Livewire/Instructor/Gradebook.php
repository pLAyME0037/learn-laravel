<?php

namespace App\Livewire\Instructor;

use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Services\GradingService;
use Livewire\Component;
use Livewire\Attributes\Layout;

class Gradebook extends Component
{
    public $classSession;
    public $grades = []; // Array to bind inputs: [enrollment_id => score]

    public function mount($classSessionId)
    {
        $this->classSession = ClassSession::with(['course', 'enrollments.student.user'])
            ->findOrFail($classSessionId);

        // Security: Ensure logged in user is the instructor
        if ($this->classSession->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this gradebook.');
        }

        // Initialize grades array
        foreach ($this->classSession->enrollments as $enrollment) {
            $this->grades[$enrollment->id] = $enrollment->final_grade; // Pre-fill existing
        }
    }

    public function saveGrade($enrollmentId, GradingService $service)
    {
        $score = $this->grades[$enrollmentId];

        // Validate
        if (!is_numeric($score) || $score < 0 || $score > 100) {
            $this->dispatch('swal:error', ['message' => 'Grade must be between 0 and 100.']);
            return;
        }

        $enrollment = Enrollment::find($enrollmentId);
        
        if ($enrollment) {
            $service->submitGrade($enrollment, $score);
            $this->dispatch('swal:success', ['message' => 'Grade saved.']);
        }
    }

    #[Layout('layouts.app', ['header' => 'Gradebook'])]
    public function render()
    {
        return view('livewire.instructor.gradebook');
    }
}