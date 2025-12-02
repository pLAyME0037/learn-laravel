<?php
namespace App\Livewire\Admin\Students;

use App\Http\Requests\StoreStudentRequest;
use App\Models\Department;
use App\Models\Gender;
use App\Models\Program;
use App\Services\StudentService;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateStudent extends Component
{
    use WithFileUploads;

    // --- 1. User Info ---
    public $name;
    public $email;
    public $username;
    public $password;
    public $password_confirmation;
    public $profile_pic;

    // --- 2. Academic Info ---
    public $department_id = '';
    public $program_id    = '';
    public $year_level    = 1;

    public $semester         = 'semester_one';
    public $current_semester = 1;
    public $registration_number;  // Nullable
    public $id_card_number;       // Nullable
    public $passport_number;      // Nullable
    public $cgpa;                 // Nullable
    public $total_credits_earned; // Nullable

    public $admission_date;
    public $enrollment_status;
    public $fee_category;
    public $expected_graduation;
    public $academic_status = 'active';

    // --- 3. Personal Info ---
    public $date_of_birth;
    public $gender_id = '';
    public $nationality;
    public $blood_group;
    public $has_disability          = false; // Checkbox
    public $has_outstanding_balance = false;
    public $disability_details;
    public $previous_education;

    // --- 4. Nested Arrays (Address & Contact) ---
    // These match the structure your Service expects
    public $contact_detail = [
        'phone_number'               => '',
        'emergency_contact_name'     => '',
        'emergency_contact_phone'    => '',
        'emergency_contact_relation' => '',
    ];

    public $address = [
        'current_address'   => '',
        'permanent_address' => '',
        'city'              => '',
        'district'          => '',
        'commune'           => '',
        'village'           => '',
        'postal_code'       => '',
    ];

    // --- 5. Data Sources ---
    public $departments;
    public $programs = []; // Empty until dept is selected
    public $genders;

    public function mount()
    {
        $this->departments = Department::active()->select('id', 'name')->get();
        $this->genders     = Gender::all();
    }

    // This runs AUTOMATICALLY when department_id changes
    public function updatedDepartmentId($value)
    {
        if ($value) {
            $this->programs = Program::query()
                ->join('majors', 'programs.major_id', '=', 'majors.id')
                ->where('majors.department_id', $value)
                ->select('programs.id', 'programs.name')
                ->get();
        } else {
            $this->programs = [];
        }
        $this->program_id = '';
    }

    public function save(StudentService $service)
    {
        // 1. Get Rules from Request
        $request = new StoreStudentRequest();
        $rules   = $request->rules();

        unset($rules['student_id']);
        unset($rules['metadata']);

        // 2. Validate
        $validatedData = $this->validate($rules);

        // 3. Call Service
        try {
            $service->registerStudent($validatedData, $this->profile_pic);

            return redirect()->route('admin.students.index')
                ->with('success', 'Student created successfully.');
        } catch (\Exception $e) {
            // Add a general error to the top of the form
            $this->addError('system_error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.students.create-student');
    }
}
