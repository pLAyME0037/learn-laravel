<?php
namespace App\Livewire\Admin;

use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Dashboard extends Component
{
    public $stats          = [];
    public $recentStudents = [];
    public $notices        = [];

    public function mount()
    {
        // 1. Fetch Real Counts
        $this->stats = [
            'total_students' => Student::count(),
            'total_staff'    => User::role('staff')->count(),
            'departments'    => Department::count(),
            'new_this_month' => Student::where('created_at', '>=', now()->startOfMonth())->count(),
        ];

        // 2. Fetch Recent Registrations
        $this->recentStudents = Student::with(['user', 'department'])
            ->latest()
            ->take(6)
            ->get();

        // 3. Mock System Notices
        $this->notices = [
            ['message' => 'System maintenance scheduled for Sunday 2 AM.', 'type' => 'warning'],
            ['message' => 'Semester 1 Grade submission deadline approaching.', 'type' => 'info'],
        ];
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'header' => 'University Overview',
        ]);
    }
}
