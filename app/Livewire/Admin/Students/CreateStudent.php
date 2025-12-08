<?php
namespace App\Livewire\Admin\Students;

use App\Http\Requests\StoreStudentRequest;
use App\Models\Commune;
use App\Models\Department;
use App\Models\District;
use App\Models\Gender;
use App\Models\Program;
use App\Models\Province;
use App\Models\Village;
use App\Services\StudentService;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateStudent extends Component
{
    use WithFileUploads;

    protected function rules() {
        return [
            // --- User Account ---
            'name'                                      => ['required', 'string', 'max:255'],
            'email'                                     => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'username'                                  => ['required', 'string', 'max:255', Rule::unique('users', 'username')],
            'password'                                  => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation'                     => ['required', 'string', 'min:8'],

            // --- Academic Info ---
            'department_id'                             => ['required', 'integer', 'exists:departments,id'],
            'program_id'                                => ['required', 'integer', 'exists:programs,id'],
            'year_level'                                => ['nullable', 'integer', 'min:1', 'max:4'],
            'semester'                                  => ['required', 'string', 'max:255'],
            'current_semester'                          => ['nullable', 'integer', 'min:1'],
            'academic_status'                           => ['required', 'string', Rule::in(['active', 'graduated', 'suspended', 'probation'])],
            'enrollment_status'                         => ['required', 'string', Rule::in(['full_time', 'part_time', 'exchange', 'study_abroad'])],
            'fee_category'                              => ['required', 'string', Rule::in(['regular', 'scholarship', 'financial_aid', 'self_financed'])],
            'admission_date'                            => ['required', 'date'],
            'expected_graduation'                       => ['required', 'date', 'after:admission_date'],
            'cgpa'                                      => ['nullable', 'numeric', 'min:0', 'max:4'],
            'total_credits_earned'                      => ['nullable', 'integer', 'min:0'],
            'has_outstanding_balance'                   => ['boolean'],

            // --- IDs (Usually generated, but if editable) ---
            // 'student_id'                             => Automaticlly fill,
            'registration_number'                       => ['nullable', 'string', 'max:255', Rule::unique('students', 'registration_number')],
            'id_card_number'                            => ['nullable', 'string', 'max:255', Rule::unique('students', 'id_card_number')],
            'passport_number'                           => ['nullable', 'string', 'max:255', Rule::unique('students', 'passport_number')],

            // --- Personal Info ---
            'date_of_birth'                             => ['required', 'date'],
            'gender_id'                                 => ['required', 'integer', 'exists:genders,id'],
            'nationality'                               => ['required', 'string', 'max:255'],

            // ✅ These were missing before:
            'blood_group'                               => ['nullable', 'string', 'max:5'],
            'previous_education'                        => ['nullable', 'string'],

            'has_disability'                            => ['boolean'],
            'disability_details'                        => ['nullable', 'string', 'required_if:has_disability,true'],

            // --- Contact Info ---
            'contact_detail.phone_number'               => ['required', 'string', 'max:20', Rule::unique('contact_details', 'phone_number')],
            'contact_detail.emergency_contact_name'     => ['nullable', 'string', 'max:255'],
            'contact_detail.emergency_contact_phone'    => ['nullable', 'string', 'max:20'],
            'contact_detail.emergency_contact_relation' => ['nullable', 'string', 'max:255'],

            // --- Address Info ---
            'address.current_address'                   => ['required', 'string'],
            'address.postal_code'                       => ['nullable', 'string', 'max:255'],
            'address.village_id'                        => ['required', 'integer', Rule::exists(Village::class, 'id')],
        ];
    }

    // --- 1. User Info ---
    public $name, $email, $username, $profile_pic;
    public $password, $password_confirmation;

    // --- 2. Academic Info ---
    public $department_id    = '';
    public $program_id       = '';
    public $year_level       = 1;
    public $semester         = 'semester_one';
    public $current_semester = 1;
    public $registration_number, $id_card_number, $passport_number;
    public $cgpa, $total_credits_earned;
    public $admission_date, $enrollment_status, $fee_category, $expected_graduation;
    public $academic_status = 'active';

    // --- 3. Personal Info ---
    public $date_of_birth;
    public $gender_id = '';
    public $nationality, $blood_group, $disability_details, $previous_education;
    public $has_disability          = false;
    public $has_outstanding_balance = false;

    // --- 4. Contact Info ---
    public $contact_detail = [
        'phone_number'               => '',
        'emergency_contact_name'     => '',
        'emergency_contact_phone'    => '',
        'emergency_contact_relation' => '',
    ];

    // --- 5. Address Info (Refactored) ---
    public $address = [
        'current_address' => '',
        'postal_code'     => '',
        'province_id'     => '',
        'district_id'     => '',
        'commune_id'      => '',
        'village_id'      => '',
    ];

    // --- 6. Dropdown Data Sources ---
    public $departments = [];
    public $programs    = [];
    public $genders     = [];

    // Location collections
    public $provinces = [];
    public $districts = [];
    public $communes  = [];
    public $villages  = [];

    public function mount()
    {
        $this->departments = Department::active()
            ->select('id', 'name')
            ->get();
        $this->genders   = Gender::all();
        $this->provinces = Province::select('id', 'name_kh', 'prov_id')
            ->orderBy('name_kh')
            ->get();
    }

    public function updatedHasDisabilityChange($value)
    {
        if (! $value) {
            $this->disability_details = null;
        }
    }

    // --- Academic Hooks ---

    public function updatedDepartmentId($value)
    {
        $this->reset('program_id', 'programs');

        if (! $value) {
            return;
        }
        $this->programs = Program::query()
            ->whereHas('major', fn($q) => $q->where('department_id', $value))
            ->select('id', 'name')
            ->get();
    }

    // --- Address Hooks (Nested Property Handling) ---

    /**
     * Triggered when $address['province_id'] changes.
     * Loads Districts, resets downstream selections.
     */
    public function updatedAddressProvinceId($value)
    {
        // Reset downstream
        $this->address['district_id'] = '';
        $this->address['commune_id']  = '';
        $this->address['village_id']  = '';

        $this->districts = [];
        $this->communes  = [];
        $this->villages  = [];

        if (! $value) {
            return;
        }
        // Find the selected Province by its Auto-ID
        $province = Province::find($value);
        if (! $province) {
            return;
        }
        $this->districts = District::query()
            ->where('province_id', $province->prov_id)
            ->select('id', 'name_kh', 'dist_id')
            ->orderBy('name_kh')
            ->get();
    }

    /**
     * Triggered when $address['district_id'] changes.
     * Loads Communes.
     */
    public function updatedAddressDistrictId($value)
    {
        // Clear child fields
        $this->address['commune_id'] = '';
        $this->address['village_id'] = '';
        $this->communes              = [];
        $this->villages              = [];

        if (! $value) {
            return;
        }
        $district = District::find($value);
        if (! $district) {
            return;
        }
        $this->communes = Commune::query()
            ->where('district_id', $district->dist_id)
            ->select('id', 'name_kh', 'comm_id') // Select needed fields
            ->orderBy('name_kh')
            ->get();
    }

    /**
     * Triggered when $address['commune_id'] changes.
     * Loads Villages.
     */
    public function updatedAddressCommuneId($value)
    {
        $this->address['village_id'] = '';
        $this->villages              = [];

        if (! $value) {
            return;
        }
        $commune = Commune::find($value);

        if (! $commune) {
            return;
        }
        // Use comm_id (Geo Code) to find villages
        $this->villages = Village::query()
            ->where('commune_id', $commune->comm_id)
            ->select('id', 'name_kh', 'vill_id')
            ->orderBy('name_kh')
            ->get();
    }

    // --- Save Logic ---

    public function save(StudentService $service)
    {
        $validatedData = $this->validate();

        try {
            $service->registerStudent($validatedData, $this->profile_pic);

            // Reset form or redirect
            $this->resetExcept('departments', 'genders', 'provinces');

            return redirect()->route('admin.students.index')
                ->with('success', 'Student created successfully.');

        } catch (\Exception $e) {
            // Log error for debugging
            Log::error('Student Creation Error: ' . $e->getMessage());
            $this->addError(''
                . 'system_error', 'Failed to create student: '
                . $e->getMessage()
            );
        }
    }

    public function render()
    {
        return view('livewire.admin.students.create-student');
    }
}
