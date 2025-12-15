<?php
namespace App\Livewire\Admin\Students;

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
    public $user    = ['name' => '', 'email' => '', 'password' => ''];
    public $profile = [
        'student_id'      => '', // Auto-generated usually
        'program_id'      => '',
        'year_level'      => 1,
        'current_term'    => 1,
        'academic_status' => 'active',
        // Dynamic Attributes
        'gender'          => '',
        'nationality'     => '',
        'dob'             => '',
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
    public $profile_pic;

    // --- Dropdowns ---
    public $programs = [];
    public $statuses = [];
    public $genders  = [];

    public function mount($studentId = null)
    {
        $this->programs = Program::pluck('name', 'id');
        $this->statuses = Dictionary::options('academic_status');
        $this->genders  = Dictionary::options('gender');

        if ($studentId) {
            $this->isEdit  = true;
            $this->student = Student::with(['user', 'address', 'contactDetail'])
                ->find($studentId);

            // Hydrate Form
            $this->user['name']     = $this->student->user->name;
            $this->user['email']    = $this->student->user->email;
            $this->user['username'] = $this->student->user->username;

            $this->profile['program_id']      = $this->student->program_id;
            $this->profile['year_level']      = $this->student->year_level;
            $this->profile['current_term']    = $this->student->current_term;
            $this->profile['academic_status'] = $this->student->academic_status;

            // Load JSON attributes
            $attrs                        = $this->student->attributes ?? [];
            $this->profile['gender']      = $attrs['gender'] ?? '';
            $this->profile['nationality'] = $attrs['nationality'] ?? '';
            $this->profile['dob']         = $attrs['dob'] ?? '';

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
        }
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

    #[On('location-selected')]
    public function setVillageId($village_id)
    {
        $this->address['village_id'] = $village_id;
    }

    public function save(StudentService $service)
    {
        $rules = [
            'user.name'          => 'required|string|max:255',
            'user.email'         => 'required|email|unique:users,email' . ($this->isEdit ? ',' . $this->student->user_id : ''),
            'user.username'      => 'required|string|unique:users,username' . ($this->isEdit ? ',' . $this->student->user_id : ''),
            'profile_pic'        => 'nullable|image|max:2048',
            'profile.program_id' => 'required|exists:programs,id',
            'profile.gender'     => 'required',
            'contact.phone'      => 'required',
            'address.village_id' => 'required', // Ensure location is picked
        ];

        if (! $this->isEdit) {
            $rules['user.password'] = 'required|min:8|confirmed';
        }

        $this->validate($rules);

        // Delegate heavy lifting to Service
        if ($this->isEdit) {
            $service->updateStudent(
                $this->student,
                $this->user,
                $this->profile,
                $this->address,
                $this->contact,
                $this->profile_pic,
            );
            return redirect()->route('admin.students.index')
                ->with('success', 'Student updated successfully.');
        } else {
            $service->registerStudent(
                $this->user,
                $this->profile,
                $this->address,
                $this->contact,
                $this->profile_pic instanceof UploadedFile ? $this->profile_pic : null,
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
