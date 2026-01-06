<?php
namespace App\Livewire\Admin\Academic;

use App\Models\Degree;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\Program;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class StructureManager extends Component
{
    use WithPagination;

    // View State
    public $activeTab = 'faculties';
    public $showModal = false;
    public $isEditing = false;
    public $itemId;

    // Filters
    public $search           = '';
    public $filterFaculty    = '';
    public $filterDepartment = '';
    public $filterDegree     = '';
    // Generic Form Bucket
    public $formData = [];

    // --- Hooks ---
    public function updatedActiveTab()
    {
        $this->resetPage();
        $this->resetFilters();
        $this->reset(['formData', 'itemId', 'isEditing', 'showModal']);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset([
            'search',
            'filterFaculty',
            'filterDepartment',
            'filterDegree',
        ]);
    }

    public function updatedFormDataMajorId($value)
    {
        // Guard: Only run for Programs tab when a value is selected
        if ($this->activeTab !== 'programs' || ! $value) {
            return;
        }

        $major = Major::with('degree')->find($value);

        if ($major) {
            $this->formData['degree_id'] = $major->degree_id;
            // Auto-suggest name
            $this->formData['name'] = "{$major->degree->name} of {$major->name}";
        }
    }

    // --- Actions ---
    public function create()
    {
        $this->reset(['formData', 'itemId', 'isEditing']);
        $this->setDefaultFormData();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->resetValidation();
        $this->reset(['formData', 'itemId', 'isEditing']);

        $modelClass = $this->getModelClass();
        $model      = $modelClass::find($id);
        if (! $model) {
            $this->dispatch('swal:error', [
                'message' => 'Record not found.',
            ]);
            return;
        }

        $this->itemId    = $id;
        $this->isEditing = true;
        $this->formData  = $model->toArray();

        if ($this->activeTab === 'programs' && $model->major_id) {
            $this->updatedFormDataMajorId($model->major_id);
        }

        if (isset($this->formData['faculty_id'])) {
            $this->formData['faculty_id'] = (string) $this->formData['faculty_id'];
        }
        if (isset($this->formData['department_id'])) {
            $this->formData['department_id'] = (string) $this->formData['department_id'];
        }
        if (isset($this->formData['major_id'])) {
            $this->formData['major_id'] = (string) $this->formData['major_id'];
        }
        if (isset($this->formData['degree_id'])) {
            $this->formData['degree_id'] = (string) $this->formData['degree_id'];
        }

        $this->showModal = true;
        // dd($this->formData);
    }

    public function save()
    {
        try {
            $this->validate();

            $model = $this->getModelClass();
            if ($this->isEditing && $this->itemId) {
                $instance = $model::findOrFail($this->itemId);
                $instance->update($this->formData);
            } else {
                $model::create($this->formData);
            }

            $this->showModal = false;
            $this->dispatch('swal:success', [
                'message' => ucfirst($this->activeTab) . ' saved.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('swal:error', [
                'message' => $e->validator->errors()->first(),
            ]);
            throw $e;
        }
    }

    public function delete($id)
    {
        $this->getModelClass()::find($id)->delete();
        $this->dispatch('swal:success', ['message' => 'Deleted.']);
    }

    // --- Helpers (DRY) ---

    private function getModelClass()
    {
        return match ($this->activeTab) {
            'faculties'   => Faculty::class,
            'departments' => Department::class,
            'majors'      => Major::class,
            'programs'    => Program::class,
            'degrees'     => Degree::class,
        };
    }

    private function setDefaultFormData()
    {
        // Initialize keys to avoid "undefined array key" errors in view
        $defaults = match ($this->activeTab) {
            'departments' => [
                'faculty_id' => '',
            ],
            'majors'      => [
                'department_id' => '',
                'degree_id'     => '',
                'cost_per_term' => 0,
            ],
            'programs'    => [
                'major_id'  => '',
                'degree_id' => '',
            ],
            default       => [],
        };

        $this->formData = array_merge(['name' => ''], $defaults);
    }

    protected function rules()
    {
        return match ($this->activeTab) {
            'faculties'   => ['formData.name' => 'required|string|max:255'],
            'degrees'     => ['formData.name' => 'required|string|max:255'],
            'departments' => [
                'formData.faculty_id' => 'required|exists:faculties,id',
                'formData.name'       => 'required|string|max:255',
                'formData.code'       => 'required|string|max:10',
            ],
            'majors'      => [
                'formData.department_id' => 'required|exists:departments,id',
                'formData.degree_id'     => 'required|exists:degrees,id',
                'formData.name'          => 'required|string|max:255',
                'formData.cost_per_term' => 'required|numeric|min:0',
            ],
            'programs'    => [
                'formData.major_id'  => 'required|exists:majors,id',
                'formData.degree_id' => 'required|exists:degrees,id',
                'formData.name'      => 'required|string|max:255',
            ],
            default       => []
        };
    }

    #[Layout('layouts.app', ['header' => 'Academic Structure'])]
    public function render()
    {
        $data = match ($this->activeTab) {
            'faculties' => Faculty::withCount('departments')
                ->when($this->search, fn($q) =>
                    $q->where('name', 'like', "%{$this->search}%")
                )
                ->orderBy('name')
                ->paginate(20),

            'departments' => Department::with('faculty')
                ->withCount('majors')
                ->when($this->search, fn($q) =>
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('code', 'like', "%{$this->search}%")
                )
                ->when($this->filterFaculty, fn($q) =>
                    $q->where('faculty_id', $this->filterFaculty)
                )
                ->orderBy('name')
                ->paginate(20),

            'majors' => Major::with(['department.faculty', 'degree'])
                ->when($this->search, fn($q) =>
                    $q->where('name', 'like', "%{$this->search}%")
                )
                ->when($this->filterDepartment, fn($q) =>
                    $q->where('department_id', $this->filterDepartment)
                )
                ->when($this->filterDegree, fn($q) =>
                    $q->where('degree_id', $this->filterDegree)
                )
                ->orderBy('name')
                ->paginate(20),

            'programs' => Program::with(['major.department', 'degree'])
                ->when($this->search, fn($q) =>
                    $q->where('name', 'like', "%{$this->search}%")
                )
                ->when($this->filterDepartment, fn($q) =>
                    $q->whereHas('major', fn($m) =>
                        $m->where('department_id', $this->filterDepartment)
                    )
                )
                ->orderBy('name')
                ->paginate(20),

            'degrees' => Degree::when($this->search, fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
            )
                ->orderBy('name')
                ->paginate(20),
        };

        return view('livewire.admin.academic.structure-manager', [
            'data'             => $data,
            'faculties_list'   => Faculty::orderBy('name')
                ->get()
                ->map(fn($f) => [
                    'id'    => $f->id,
                    'label' => $f->name,
                ])->toArray(),
            'departments_list' => Department::orderBy('name')
                ->get()
                ->map(fn($f) => [
                    'id'    => $f->id,
                    'label' => $f->name,
                ])->toArray(),
            'degrees_list'     => Degree::orderBy('name')
                ->get()
                ->map(fn($f) => [
                    'id'    => $f->id,
                    'label' => $f->name,
                ])->toArray(),
            'majors_list'      => Major::orderBy('name')->select('id', 'name')
                ->get()
                ->map(fn($f) => [
                    'id'    => $f->id,
                    'label' => $f->name,
                ])->toArray(),
        ]);
    }
}
