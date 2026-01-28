<?php
namespace App\Livewire\Admin\Students;

use App\Models\Department;
use App\Models\Program;
use App\Models\Student;
use App\Tables\Action;
use App\Tables\Column;
use App\Tables\Field;
use App\Tables\Table;
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

    public function buildTable($query) {
        return Table::make($query)->columns([
            // 1. ID Column (Calculation)
            Column::make('#')->stack([
                Field::index()->css('font-bold text-indigo-600'),
            ]),

            // 2. Identity (Profile Image + Name)
            Column::make('Identity')->grid('max-content 1fr', [
                Field::make('user')->component('profile-image', fn($user) => [
                    'size' => 'sm',
                    'src'  => $user?->profile_picture_url,
                    'alt'  => $user?->name ?? 'N/A',
                ]),
                Column::make('')->stack([
                    Field::make('user.name')->bold(),
                    Field::make('student_id')->small(),
                    Field::make('user.email')->small(),
                ]),
            ]),

            Column::make('Department')->stack([
                Field::make('program.name')->bold(),
                Field::make('program.major.department.name')->small(),
            ]),

            // 3. Address (Complex HTML with Orange Text)
            Column::make('Address')->stack([
                Field::make('address')
                ->view('components.table.Student.address-block'),
            ]),

            // 4. Progress (Calculation on Fly)
            Column::make('Progress')->center()->stack([
                Field::make('current_term')
                ->view('components.table.Student.term'),
            ]),

            Column::make('Status')->center()->stack([
                Field::make('academic_status')
                ->format(fn($val, $row) => !empty($row['deleted_at']) ? 'Trashed' : $val)
                ->view('components.table.Student.status-badge'),
            ]),

            Column::make('Action')->right()->stack([
                Field::make('id')->actions([

                Action::link(fn($row) => route('admin.students.show', $row['id']))
                    ->icon('heroicon-o-eye') // Much cleaner!
                    ->color('text-gray-500')
                    ->when(fn($row) => empty($row['deleted_at'])),

                Action::link(fn($row) => route('admin.students.edit', $row['id']))
                    ->icon('heroicon-o-pencil-square')
                    ->color('text-blue-500')
                    ->when(fn($row) => empty($row['deleted_at'])),

                Action::button('confirmDelete')
                    ->icon(fn($row) => ($row['deleted_at'])
                    ? 'heroicon-o-arrow-path'
                    : 'heroicon-o-trash'
                    )
                    ->color(fn($row) => ($row['deleted_at'])
                    ? 'text-green-600 hover:text-green-800'
                    : 'text-red-500 hover:text-red-700'
                    ),
                ]),
            ]),
        ]);
    }

    /**
     * Global hook: Runs when ANY property updates.
     * $property = 'search', 'filterProgram', 'filterStatus'.
     */
    public function updated($property) {
        $filters = [
            'search',
            'filterProgram',
            'filterStatus',
        ];

        if (in_array($property, $filters)) {
            $this->resetPage();
        }
    }

    public function updatedFilterDepartment() {
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

            // Fetch the Trashed User Model
            $user = $student->user()->withTrashed()->first();
            if ($user && $user->trashed()) {
                $user->restore();
            }

            // Fetch Trashed Address
            $address = $student->address()->withTrashed()->first();
            if ($address && $address->trashed()) {
                $address->restore();
            }

            // Fetch Trashed Contact
            $contact = $student->contactDetail()->withTrashed()->first();
            if ($contact && $contact->trashed()) {
                $contact->restore();
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
        // dump($this->filterDepartment, $this->filterProgram, $this->search, $this->filterStatus);
        $query = Student::applyFilters([
            'search'          => $this->search,
            'department_id'   => $this->filterDepartment,
            'program_id'      => $this->filterProgram,
            'academic_status' => $this->filterStatus,
        ])->orderByDesc('created_at');

        $table = $this->buildTable($query); // Student table

        $departments = Department::orderBy('name')
            ->get()
            ->map(fn($m) => [
                'id'    => $m->id,
                'label' => "{$m->name}",
            ]);

        $programs = Program::query()
            ->with('major')
            ->with('degree')
            ->when($this->filterDepartment, function ($q) {
                $q->whereHas('major', fn($m) =>
                    $m->where('department_id', $this->filterDepartment)
                );
            })
            ->orderBy('name')
            ->get()
            ->map(fn($m) => [
                'id'    => $m->id,
                'label' => "{$m->name}",
                'sub'        => "{$m->major->name} • ({$m->degree->name})",
            ]);

        return view('livewire.admin.students.student-list', compact(
            'table',
            'departments',
            'programs',
        ));
    }
}
