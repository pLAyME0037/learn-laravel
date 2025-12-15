<?php
namespace App\Livewire\Admin\Students;

use App\Models\Department;
use App\Models\Program;
use App\Models\Student;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class StudentList extends Component
{
    use WithPagination;

    // Filter Properties
    public $search           = '';
    public $filterDepartment = '';
    public $filterProgram    = '';
    public $filterStatus     = '';

    // Reset pagination when any filter changes
    public function updatedSearch()
    {$this->resetPage();}
    public function updatedFilterProgram()
    {$this->resetPage();}
    public function updatedFilterStatus()
    {$this->resetPage();}

    // DEPENDENT DROPDOWN LOGIC
    // When Department changes, clear the Program selection
    public function updatedFilterDepartment()
    {
        $this->filterProgram = '';
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'filterDepartment', 'filterProgram', 'filterStatus']);
        $this->resetPage();
    }

    #[Layout('layouts.app', ['header' => 'Students Management'])]
    public function render()
    {
        // 1. Prepare Filters Array
        $filters = [
            'search'          => $this->search,
            'department_id'   => $this->filterDepartment,
            'program_id'      => $this->filterProgram,
            'academic_status' => $this->filterStatus,
        ];

        // 2. Use Scope
        $students = Student::applyFilters($filters)
            ->orderByDesc('created_at')
            ->paginate(10);

        // 3. Load Dropdown Data
        $departments = Department::orderBy('name')->get();

        $programs = Program::query()
            ->when($this->filterDepartment, function ($q) {
                $q->whereHas('major', fn($m) =>
                    $m->where('department_id', $this->filterDepartment)
                );
            })
            ->orderBy('name')
            ->get();

        return view('livewire.admin.students.student-list', [
            'students'    => $students,
            'departments' => $departments,
            'programs'    => $programs,
        ]);
    }
}
