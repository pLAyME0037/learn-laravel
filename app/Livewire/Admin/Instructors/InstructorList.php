<?php
namespace App\Livewire\Admin\Instructors;

use App\Models\Instructor;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class InstructorList extends Component
{
    use WithPagination;

    public $search = '';

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
        $instructors = Instructor::with(['user', 'department'])
            ->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id')
            ->paginate(10);

        return view('livewire.admin.instructors.instructor-list', [
            'instructors' => $instructors,
        ]);
    }
}
