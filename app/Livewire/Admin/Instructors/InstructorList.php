<?php
namespace App\Livewire\Admin\Instructors;

use App\Models\Department;
use App\Models\Instructor;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class InstructorList extends Component
{
    use WithPagination;

    public $search           = '';
    public $filterDepartment = '';

    public function updatedFilterDepartment()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $instructor = Instructor::find($id);
        if ($instructor) {
            $instructor->user->delete();
            $instructor->delete();
            session()->flash('success', 'Instructor deleted.');
        }
    }

    #[Layout('layouts.app', ['header' => 'Instructors'])]
    public function render()
    {
        $query = Instructor::with(['user', 'department'])
            ->whereHas('user', fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
            );

        if ($this->filterDepartment) {
            $query->where('department_id', $this->filterDepartment);
        }

        $instructors = $query->orderBy('id')->paginate(10);
        $departments = Department::orderBy('name')->pluck('name', 'id');

        return view('livewire.admin.instructors.instructor-list', [
            'instructors' => $instructors,
            'departments' => $departments,
        ]);
    }
}
