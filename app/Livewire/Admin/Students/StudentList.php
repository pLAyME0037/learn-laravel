<?php
namespace App\Livewire\Admin\Students;

use App\Models\Department;
use App\Models\Program;
use App\Models\Student;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
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

    public function updatedFilterDepartment()
    {
        $this->filterProgram = '';
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset([
            'search',
            'filterDepartment',
            'filterProgram',
            'filterStatus',
        ]);
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $student = Student::withTrashed()->find($id);
        if (! $student) {
            return;
        }

        // Default Config (Active Student)
        $config = [
            'title'         => 'Manage Student',
            'text'          => "Action for: " . ($student->user?->name ?? 'Unknown Student'),
            'id'            => $id,
            'showDeny'      => true, // "Force Delete" button
            'showCancel'    => true, // "Cancel" button
            'confirmText'   => 'Trash',
            'denyText'      => 'Delete',
            'confirmAction' => 'delete',
            'denyAction'    => 'force_delete',
        ];

        if ($student->trashed()) {
            $config['confirmText']   = 'Restore Student';
            $config['confirmAction'] = 'restore';
            $config['confirmColor']  = '#10b981'; // Green
        }

        $this->dispatch('swal:multi-action', $config);
    }

    #[On('executeAction')]
    public function executeAction($id, $action)
    {
        // 1. Find Student (With Trashed)
        $student = Student::withTrashed()->find($id);

        if (! $student) {
            $this->dispatch('swal:error', ['message' => 'Student not found.']);
            return;
        }

        switch ($action) {
        case 'delete':
            $student->delete();
            $student->user()->delete();
            $student->address()->delete();
            $student->contactDetail()->delete();
            $msg = 'Student and User account moved to trash.';
        break;

        case 'restore':
            $student->restore();
            $user = $student->user()->withTrashed()->exists();
            if ($user) {
                $student->user()->restore();
            }

            $address = $student->address()->withTrashed()->exists();
            if ($address) {
                $student->address()->restore();
            }

            $contactDetail = $student->contactDetail()->withTrashed()->exists();
            if ($contactDetail) {
                $student->contactDetail()->restore();
            }

            $msg = 'Student record restored.';
        break;

        case 'force_delete':
            if ($student->user) {
                $student->user()->forceDelete();
            }

            $student->forceDelete();
            $msg = 'Student permanently deleted.';
        break;
        }

        $this->dispatch('swal:success', ['message' => $msg]);
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
