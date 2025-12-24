<?php
namespace App\Livewire\Admin\Students;

use App\Models\Department;
use App\Models\Dictionary;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use App\Services\StudentService;
use Illuminate\Http\UploadedFile;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class StudentForm extends Component
{
    use WithFileUploads;

    public ?Student $student = null;
    public $isEdit           = false;

    // --- Form State ---
    public $user = ['name' => '', 'email' => '', 'password' => ''];
    public $profile_pic;
    public $profile = [
        'student_id'         => '', // Auto-generated
        'program_id'         => '',
        'year_level'         => 1,
        'current_term'       => 1,
        'academic_status'    => 'active',
        'has_disability'     => false,
        'disability_details' => '',
        // Dynamic Attributes
        'dob'                => '',
        'gender'             => '',
        'nationality'        => '',
        'blood_group'        => '',
    ];
    public $address = [
        'current_address' => '',
        'postal_code'     => '',
        'village_id'      => null,
    ];
    public $contact = [
        'phone'           => '',
        'emergency_name'  => '',
        'emergency_phone' => '',
    ];

    // --- Dropdowns ---
    public $departments = [];
    public $programs    = [];
    public $statuses    = [];
    public $genders     = [];
    public $bloodGroups = [];

    public function mount($studentId = null)
    {
        $this->departments = Department::orderby('name')->pluck('name', 'id');
        $this->programs    = Program::orderby('name')->pluck('name', 'id');
        $this->statuses    = Dictionary::options('academic_status');
        $this->genders     = Dictionary::options('gender');
        $this->bloodGroups = Dictionary::options('blood_group');

        if (! $studentId) {
            return;
        }
        $this->isEdit  = true;
        $this->student = Student::with(['user', 'address', 'contactDetail'])
            ->find($studentId);

        // Hydrate Form
        $this->user['name']     = $this->student->user->name;
        $this->user['email']    = $this->student->user->email;
        $this->user['username'] = $this->student->user->username;

        $this->profile['has_disability']     = $this->student->user->student->has_disability;
        $this->profile['disability_details'] = $this->student->user->student->disability_details;

        if ($this->student->program) {
            $this->profile['department_id'] = $this->student->program->major->department_id;
        }

        $this->profile['program_id']      = $this->student->program_id;
        $this->profile['year_level']      = $this->student->year_level;
        $this->profile['current_term']    = $this->student->current_term;
        $this->profile['academic_status'] = $this->student->academic_status;

        // Load JSON attributes
        $attrs                        = $this->student->attributes ?? [];
        $this->profile['dob']         = $attrs['dob'] ?? '';
        $this->profile['gender']      = $attrs['gender'] ?? '';
        $this->profile['nationality'] = $attrs['nationality'] ?? '';
        $this->profile['blood_group'] = $attrs['blood_group'] ?? '';

        if ($this->student->address) {
            $this->address = $this->student->address->only([
                'current_address',
                'postal_code',
                'village_id',
            ]);
        }
        if ($this->student->contactDetail) {
            $this->contact = $this->student->contactDetail->only([
                'phone',
                'emergency_name',
                'emergency_phone',
            ]);
        }
        $this->updatedProfileDepartmentId($this->profile['department_id']);
    }

    public function generateUsername()
    {
        $base = '';
        if (! empty($this->user['email'])) {
            $base = explode('@', $this->user['email'])[0];
        } elseif (! empty($this->user['name'])) {
            $base = strtolower(str_replace(' ', '.', $this->user['name']));
        } else {
            $base = 'student' . rand(1000, 9999);
        }

        $username = $base;
        while (User::where('username', $username)->exists()) {
            $username = $base . rand(100, 999);
        }

        $this->user['username'] = $username;
    }

    public function updatedProfileDepartmentId($value)
    {
        // Reset Program if department changes manually
        if (! $this->isEdit && $value) {
            $this->profile['program_id'] = '';
        }

        if ($value) {
            // Load programs belonging to this department (via Major)
            $this->programs = Program::whereHas('major', fn($q) =>
                $q->where('department_id', $value))
                ->pluck('name', 'id');
        } else {
            // Or load all if none selected (optional)
            $this->programs = Program::orderBy('name')->pluck('name', 'id');
        }
    }
    // Livewire Hook: Runs automatically when 'profile.has_disability' changes
    public function updatedProfileHasDisability($value)
    {
        // Data Safety: If unchecked, wipe the sensitive detail immediately
        if (! $value) {
            $this->profile['disability_details'] = null;
        }
    }

    #[On('location-selected')]
    public function setVillageId($village_id)
    {
        $this->address['village_id'] = $village_id;
    }

    public function save(StudentService $service)
    {
        $rules = [
            'user.name'                  => 'required|string|max:255',
            'user.email'                 => 'required|email|unique:users,email'
            . ($this->isEdit ? ',' . $this->student->user_id : ''),
            'user.username'              => 'required|string|unique:users,username'
            . ($this->isEdit ? ',' . $this->student->user_id : ''),
            'profile_pic'                => 'nullable|image|max:2048',
            'profile.program_id'         => 'required|exists:programs,id',
            'profile.dob'                => 'required',
            'profile.gender'             => 'required',
            'profile.nationality'        => 'required',
            'profile.blood_group'        => 'nullable',
            'profile.has_disability'     => 'nullable|boolean',
            'profile.disability_details' => 'nullable|string|required_if:profile.has_disability,true',
            'contact.phone'              => 'required',
            'address.village_id'         => 'required', // Ensure location is picked
        ];

        if (! $this->isEdit) {
            $rules['user.password'] = 'required|min:8|confirmed';
        }

        $this->validate($rules);

        // Delegate heavy lifting to Service
        if ($this->isEdit) {
            $newStudent = $service->updateStudent(
                $this->student,
                $this->user,
                $this->profile,
                $this->address,
                $this->contact,
                $this->profile_pic,
            );
            return redirect()->route('admin.students.index')
                ->with('success', "Student {$newStudent->student_id} created successfully!");;
        } else {
            $service->registerStudent(
                $this->user,
                $this->profile,
                $this->address,
                $this->contact,
                $this->profile_pic instanceof UploadedFile
                    ? $this->profile_pic
                    : null,
            );
            return redirect()->route('admin.students.index')
                ->with('success', 'Student created successfully.');
        }
    }

    #[Layout('layouts.app', ['header' => 'Student Management'])]
    public function render()
    {
        return view('livewire.admin.students.student-form');
    }
}
