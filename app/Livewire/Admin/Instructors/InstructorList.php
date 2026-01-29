<?php
namespace App\Livewire\Admin\Instructors;

use App\Models\Department;
use App\Models\Instructor;
use App\Tables\Action;
use App\Tables\Column;
use App\Tables\Field;
use App\Tables\Table;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class InstructorList extends Component
{
    use WithPagination;

    // Filters
    public $search           = '';
    public $filterDepartment = '';
    public $filterStatus     = '';

    // --- Hooks ---
    public function updated($property) {
        $filters = [
            'search',
            'filterDepartment',
            'filterStatus',
        ];

        if (in_array($property, $filters)) {
            $this->resetPage();
        }
    }

    public function resetFilters() {
        $this->reset(['search', 'filterDepartment', 'filterStatus']);
        $this->resetPage();
    }

    // --- Table Definition (The Core) ---

    public function buildTable($query) {
        return Table::make($query)->columns([

            Column::make('#')->stack([
                Field::index()->css('font-bold text-indigo-600'),
            ]),

            Column::make('Instructor')->grid('max-content 1fr', [
                Field::make('user')->component('profile-image', fn($user) => [
                    'size' => 'sm',
                    'src'  => $user?->profile_picture_url,
                    'alt'  => $user?->name ?? 'N/A',
                ]),
                Column::make('')->stack([
                    Field::make('user.name')->bold(),
                    Field::make('staff_id')->small()->css('font-mono'),
                    Field::make('user.email')->small(),
                ]),
            ]),

            Column::make('Department')->stack([
                Field::make('department.name')->bold(),
                Field::make('attributes.specialization')->label('specialize: '),
                Field::make('contactDetail.phone')->small(),
                Field::make('contactDetail.emergency_phone')
                    ->css('text-red-400 dark:text-red-400')
                    ->small(),
            ]),

            Column::make('Working hours')->stack([
                Field::make('office_hours')
                ->html(fn($val) => $val)
                ->format(fn($val) => str_replace(',', '<br>', $val))
                ->css('text-white')
                ->small(),
            ]),

            // Column::make('Contact')->stack([
            // ]),

            Column::make('Action')->right()->stack([
                Field::make('id')->actions([

                    // View
                    Action::link(fn($row) => route('admin.instructors.show', $row->id))
                        ->icon('heroicon-o-eye')
                        ->color('text-gray-500')
                        ->when(fn($row) => ! $row->trashed()),

                    // Edit
                    Action::link(fn($row) => route('admin.instructors.edit', $row->id))
                        ->icon('heroicon-o-pencil-square')
                        ->color('text-blue-500')
                        ->when(fn($row) => ! $row->trashed()),

                    // Delete / Restore (Smart)
                    Action::button('confirmDelete')
                        ->icon(fn($row) => $row->trashed()
                                ? 'heroicon-o-arrow-path' // Restore Icon
                                : 'heroicon-o-trash'      // Trash Icon
                        )
                        ->color(fn($row) => $row->trashed()
                                ? 'text-green-600 hover:text-green-800'
                                : 'text-red-500 hover:text-red-700'
                        ),
                ]),
            ]),

        ]);
    }

    // --- Action Logic ---
    public function confirmDelete($id) {
        $instructor = Instructor::withTrashed()->find($id);

        if (! $instructor) { return; }

        $name = $instructor->user->name ?? 'Unknown Instructor';

        // Base Config (Soft Delete)
        $config = [
            'title' => 'Manage Instructor',
            'text'  => "Action for: {$name}",
            'id'            => $id,
            'showDeny'      => true,
            'showCancel'    => true,
            'confirmText'   => 'Trash',
            'denyText'      => 'Delete Forever',
            'confirmAction' => 'delete',
            'denyAction'    => 'force_delete',
        ];

        // Trashed Config (Restore)
        if ($instructor->trashed()) {
            $config['confirmText']   = 'Restore Instructor';
            $config['confirmAction'] = 'restore';
            $config['confirmColor']  = '#10b981'; // Green
        }

        $this->dispatch('swal:multi-action', $config);
    }

    #[On('executeAction')]
    public function executeAction($id, $action) {
        $instructor = Instructor::withTrashed()->find($id);

        if (! $instructor) {
            $this->dispatch('swal:error', ['message' => 'Record not found.']);
            return;
        }

        switch ($action) {
        case 'delete': // Soft Delete
            $instructor->delete();
            $instructor->user->delete();
            $instructor->address()->delete();
            $instructor->contactDetail()->delete();

            $msg = 'Instructor moved to trash.';
        break;

        case 'restore': // Restore
            $instructor->restore();
            $instructor->user()->withTrashed()->first()?->restore();
            $instructor->address()->withTrashed()->first()?->restore();
            $instructor->contactDetail()->withTrashed()->first()?->restore();

            $msg = 'Instructor restored.';
        break;

        case 'force_delete': // Hard Delete
            $instructor->forceDelete();

            $msg = 'Instructor permanently deleted.';
        break;
        }

        $this->dispatch('swal:success', ['message' => $msg]);
    }

    #[Layout('layouts.app', ['header' => 'Instructors'])]
    public function render() {
        // 1. Build Query with Eager Loading (Including Trashed for restore logic)
        $query = Instructor::with([
            'user' => fn($q) => $q->withTrashed(),
            'department',
            'contactDetail' => fn($q) => $q->withTrashed(),
        ]);

        // 2. Apply Filters
        $query->whereHas('user', function ($q) {
            $q->withTrashed() // Important: Search even if user is soft deleted
                ->where(fn($sub) =>
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                );
        });

        if ($this->filterDepartment) {
            $query->where('department_id', $this->filterDepartment);
        }

        // Filter by Status (Active vs Trashed)
        if ($this->filterStatus === 'trashed') {
            $query->onlyTrashed();
        }

        // 3. Build Table
        $instructors = $this->buildTable($query);

        $departments = Department::orderBy('name')->pluck('name', 'id');

        // 4. Return View
        return view('livewire.admin.instructors.instructor-list', compact(
            'instructors',
            'departments',
        ));
    }
}
