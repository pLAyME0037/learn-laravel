<?php

namespace App\Livewire\Admin\Academic;

use App\Models\Degree;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\Program;
use Livewire\Component;
use Livewire\Attributes\Layout;

class StructureManager extends Component
{
    // Tabs: faculties, departments, degrees, majors, programs
    public $activeTab = 'faculties'; 
    public $showModal = false;
    public $isEditing = false;
    public $itemId;

    // Generic Form Bucket
    public $formData = [];

    // --- Validation Rules ---
    protected function rules()
    {
        if ($this->activeTab === 'faculties') return [
            'formData.name' => 'required|string|max:255'
        ];
        
        if ($this->activeTab === 'degrees') return [
            'formData.name' => 'required|string|max:255'
        ];

        if ($this->activeTab === 'departments') return [
            'formData.faculty_id' => 'required|exists:faculties,id',
            'formData.name' => 'required|string|max:255',
            'formData.code' => 'required|string|max:10',
        ];

        if ($this->activeTab === 'majors') return [
            'formData.department_id' => 'required|exists:departments,id',
            'formData.degree_id' => 'required|exists:degrees,id',
            'formData.name' => 'required|string|max:255',
            'formData.cost_per_term' => 'required|numeric|min:0',
        ];

        if ($this->activeTab === 'programs') return [
            'formData.major_id' => 'required|exists:majors,id',
            'formData.degree_id' => 'required|exists:degrees,id',
            'formData.name' => 'required|string|max:255',
        ];

        return [];
    }

    // --- Actions ---

    public function create()
    {
        $this->reset(['formData', 'itemId', 'isEditing']);
        
        // Set default selects if possible to avoid empty required fields
        if ($this->activeTab === 'departments') $this->formData['faculty_id'] = '';
        if ($this->activeTab === 'majors') { 
            $this->formData['department_id'] = ''; 
            $this->formData['degree_id'] = ''; 
        }
        if ($this->activeTab === 'programs') {
            $this->formData['major_id'] = '';
            $this->formData['degree_id'] = '';
        }

        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->itemId = $id;
        $this->isEditing = true;

        if ($this->activeTab === 'faculties') $this->formData = Faculty::find($id)->toArray();
        if ($this->activeTab === 'degrees') $this->formData = Degree::find($id)->toArray();
        if ($this->activeTab === 'departments') $this->formData = Department::find($id)->toArray();
        if ($this->activeTab === 'majors') $this->formData = Major::find($id)->toArray();
        if ($this->activeTab === 'programs') $this->formData = Program::find($id)->toArray();

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->activeTab === 'faculties') Faculty::updateOrCreate(['id' => $this->itemId], $this->formData);
        if ($this->activeTab === 'degrees') Degree::updateOrCreate(['id' => $this->itemId], $this->formData);
        if ($this->activeTab === 'departments') Department::updateOrCreate(['id' => $this->itemId], $this->formData);
        if ($this->activeTab === 'majors') Major::updateOrCreate(['id' => $this->itemId], $this->formData);
        if ($this->activeTab === 'programs') Program::updateOrCreate(['id' => $this->itemId], $this->formData);

        $this->showModal = false;
        session()->flash('success', ucfirst($this->activeTab) . ' saved successfully.');
    }

    public function delete($id)
    {
        if ($this->activeTab === 'faculties') Faculty::find($id)->delete();
        if ($this->activeTab === 'degrees') Degree::find($id)->delete();
        if ($this->activeTab === 'departments') Department::find($id)->delete();
        if ($this->activeTab === 'majors') Major::find($id)->delete();
        if ($this->activeTab === 'programs') Program::find($id)->delete();
    }

    // --- Helper for Dynamic Dropdowns ---
    
    // When Major changes in Program form, auto-select Degree
    public function updatedFormDataMajorId($value)
    {
        if ($this->activeTab === 'programs' && $value) {
            $major = Major::find($value);
            if ($major) {
                $this->formData['degree_id'] = $major->degree_id;
                // Auto-suggest name
                $degreeName = Degree::find($major->degree_id)->name;
                $this->formData['name'] = "{$degreeName} of {$major->name}";
            }
        }
    }

    #[Layout('layouts.app', ['header' => 'Academic Structure'])]
    public function render()
    {
        $data = [];
        // Load data based on active tab with eager loading
        if ($this->activeTab === 'faculties') $data = Faculty::withCount('departments')->orderBy('name')->get();
        if ($this->activeTab === 'degrees') $data = Degree::orderBy('name')->get();
        if ($this->activeTab === 'departments') $data = Department::with('faculty')->orderBy('name')->get();
        if ($this->activeTab === 'majors') $data = Major::with(['department', 'degree'])->orderBy('name')->get();
        if ($this->activeTab === 'programs') $data = Program::with(['major', 'degree'])->orderBy('name')->get();

        return view('livewire.admin.academic.structure-manager', [
            'data' => $data,
            'faculties_list' => Faculty::orderBy('name')->pluck('name', 'id'),
            'departments_list' => Department::orderBy('name')->pluck('name', 'id'),
            'degrees_list' => Degree::pluck('name', 'id'),
            'majors_list' => Major::orderBy('name')->pluck('name', 'id'),
        ]);
    }
}