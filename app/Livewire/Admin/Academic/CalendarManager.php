<?php
namespace App\Livewire\Admin\Academic;

use App\Models\AcademicYear;
use App\Models\Semester;
use App\Services\StudentService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

class CalendarManager extends Component
{
    // List Data
    public $years;

    // Form Data (Shared for Create/Edit)
    public $form = [
        'id'               => null,
        'academic_year_id' => null, // Only for semester
        'name'             => '',
        'start_date'       => '',
        'end_date'         => '',
    ];

    // UI State
    public $isEditing   = false;
    public $editingType = null; // 'year' or 'semester'
    public $showModal   = false;

    public function mount()
    {
        $this->refreshData();
    }

    public function refreshData()
    {
        $this->years = AcademicYear::with('semesters')
            ->orderByDesc('start_date')
            ->get();
    }

    // --- Actions: Create/Edit Year ---

    public function createYear()
    {
        $this->resetForm();
        $this->editingType = 'year';
        $this->showModal   = true;
    }

    public function editYear($id)
    {
        $year       = AcademicYear::find($id);
        $this->form = [
            'id'         => $year->id,
            'name'       => $year->name,
            'start_date' => $year->start_date->format('Y-m-d'),
            'end_date'   => $year->end_date->format('Y-m-d'),
        ];
        $this->isEditing   = true;
        $this->editingType = 'year';
        $this->showModal   = true;
    }

    // --- Actions: Create/Edit Semester ---

    public function createSemester($yearId)
    {
        $this->resetForm();
        $this->form['academic_year_id'] = $yearId;
        $this->editingType              = 'semester';
        $this->showModal                = true;
    }

    public function editSemester($id)
    {
        $sem        = Semester::find($id);
        $this->form = [
            'id'               => $sem->id,
            'academic_year_id' => $sem->academic_year_id,
            'name'             => $sem->name,
            'start_date'       => $sem->start_date->format('Y-m-d'),
            'end_date'         => $sem->end_date->format('Y-m-d'),
        ];
        $this->isEditing   = true;
        $this->editingType = 'semester';
        $this->showModal   = true;
    }

    // --- Save Logic ---

    public function save()
    {
        // 1. Validation
        $rules = [
            'form.name'       => 'required|string',
            'form.start_date' => 'required|date',
            'form.end_date'   => 'required|date|after:form.start_date',
        ];

        $this->validate($rules);

        // 2. Logic for Year vs Semester
        if ($this->editingType === 'year') {
            AcademicYear::updateOrCreate(
                ['id' => $this->form['id']],
                [
                    'name'       => $this->form['name'],
                    'start_date' => $this->form['start_date'],
                    'end_date'   => $this->form['end_date'],
                ]
            );
        } elseif ($this->editingType === 'semester') {
            Semester::updateOrCreate(
                ['id' => $this->form['id']],
                [
                    'academic_year_id' => $this->form['academic_year_id'],
                    'name'             => $this->form['name'],
                    'start_date'       => $this->form['start_date'],
                    'end_date'         => $this->form['end_date'],
                ]
            );
        }

        $this->showModal = false;
        $this->refreshData();
        session()->flash('success', ucfirst($this->editingType) . ' saved successfully.');
    }

    // --- Delete Logic ---

    public function deleteYear($id)
    {
        $year = AcademicYear::find($id);
        if ($year->semesters()->count() > 0) {
            $this->dispatch('notify-error', 'Cannot delete Year with existing semesters.');
            return;
        }
        $year->delete();
        $this->refreshData();
    }

    public function deleteSemester($id)
    {
        Semester::find($id)->delete();
        $this->refreshData();
    }

    #[On('closeSemester')]
    public function closeSemester(StudentService $service)
    {
        // 1. Deactivate current semesters
        // Semester::query()->update(['is_active' => false]);

        // 2. Promote Students
        try {
            $count = $service->promoteAllStudents();

            $this->dispatch('swal:success', [
                'message' => "Semester Closed. {$count} students have been promoted to the next term.",
            ]);

        } catch (\Exception $e) {
            $this->dispatch('swal:error', ['message' => $e->getMessage()]);
        }
    }

    public function confirmCloseSemester()
    {
        $this->dispatch('swal:confirm', [
            'title'              => 'Close Semester?',
            'text'               => "This will advance ALL active students to their next Term (e.g. Term 1 -> Term 2). This cannot be undone easily.",
            'method'             => 'closeSemester',
            'confirmButtonText'  => 'Yes, Promote Students',
            'confirmButtonColor' => '#d33', // Red for caution
        ]);
    }

    // --- Helper: Toggle Active ---

    public function toggleActiveSemester($id)
    {
        Semester::query()->update(['is_active' => false]);
        Semester::where('id', $id)->update(['is_active' => true]);

        $sem = Semester::find($id);
        AcademicYear::query()->update(['is_current' => false]);
        $sem->academicYear->update(['is_current' => true]);

        $this->refreshData();
    }

    private function resetForm()
    {
        $this->form = [
            'id'               => null,
            'academic_year_id' => null,
            'name'             => '',
            'start_date'       => '',
            'end_date'         => '',
        ];
        $this->isEditing   = false;
        $this->editingType = null;
    }

    #[Layout('layouts.app', ['header' => 'Academic Calendar'])]
    public function render()
    {
        return view('livewire.admin.academic.calendar-manager');
    }
}
