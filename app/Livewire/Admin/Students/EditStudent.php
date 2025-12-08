<?php
namespace App\Livewire\Admin\Students;

use App\Http\Requests\UpdateStudentRequest;
use App\Models\Commune;
use App\Models\Department;
use App\Models\District;
use App\Models\Gender;
use App\Models\Program;
use App\Models\Province;
use App\Models\Student;
use App\Models\Village;
use App\Services\StudentService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditStudent extends Component
{
    use WithFileUploads;

    public function rules(): array {
        $student = Student::with("contactDetail")
            ->findOrFail($this->studentId);
        $userId          = $student->user_id;
        $contactDetailId = DB::table('contact_details')
            ->where('contactable_type', 'like', '%Student')
            ->where(function ($query) use ($student, $userId) {
                $query->where('contactable_id', $student->id)
                    ->orWhere('contactable_id', $userId);
            })
            ->value('id');
        $ignoreId = $contactDetailId ?? 0;

        return [
            "name"                                      => ["required", "string", "max:255"],
            "email"                                     => ["required", "string", "email", "max:255", Rule::unique("users", "email")->ignore($userId)],
            "username"                                  => ["required", "string", "max:255", Rule::unique("users", "username")->ignore($userId)],
            "department_id"                             => ["required", "integer", Rule::exists("departments", "id")],
            "program_id"                                => ["required", "integer", Rule::exists("programs", "id")],
            "year_level"                                => ["nullable", "integer", "min:1", "max:4"],
            // "student_id"                                => Automaaticlly fill,
            "registration_number"                       => ["nullable", "string", "max:255", Rule::unique("students", "registration_number")->ignore($this->studentId)],
            "date_of_birth"                             => ["required", "date"],
            "gender_id"                                 => ["required", "integer", Rule::exists("genders", "id")],
            "nationality"                               => ["required", "string", "max:255"],
            "id_card_number"                            => ["nullable", "string", "max:255", Rule::unique("students", "id_card_number")->ignore($this->studentId)],
            "passport_number"                           => ["nullable", "string", "max:255", Rule::unique("students", "passport_number")->ignore($this->studentId)],
            "admission_date"                            => ["required", "date"],
            "expected_graduation"                       => ["required", "date", "after:admission_date"],
            "semester"                                  => ["required", "string", "max:255"],
            "current_semester"                          => ["nullable", "integer", "min:1"],
            "cgpa"                                      => ["nullable", "numeric", "min:0", "max:4"],
            "total_credits_earned"                      => ["nullable", "integer", "min:0"],
            "academic_status"                           => ["required", "string", Rule::in(["active", "graduated", "suspended", "probation"])],
            "enrollment_status"                         => ["required", "string", Rule::in(["full_time", "part_time", "exchange", "study_abroad"])],
            "fee_category"                              => ["required", "string", Rule::in(["regular", "scholarship", "financial_aid", "self_financed"])],
            "has_outstanding_balance"                   => ["boolean"],
            "previous_education"                        => ["nullable", "string"],
            "blood_group"                               => ["nullable", "string", "max:5"],
            "has_disability"                            => ["boolean"], // Not required, handled by preprocessing
            "disability_details"                        => ["nullable", "string", "required_if:has_disability,true"],
            // "metadata"                                  => [Not for fill. plan to use for other thing if possible,

            // Password fields are not updated via student profile edit, handle separately if needed
            // "password"                                  => ["required", "string", "min:8", "confirmed"],
            // "password_confirmation"                     => ["required", "string", "min:8"], // Explicitly added for "confirmed" rule

            // Contact Detail (nested)
            "contact_detail.phone_number"               => ["required", "string", "max:20", Rule::unique("contact_details", "phone_number")->ignore($ignoreId)],
            "contact_detail.emergency_contact_name"     => ["nullable", "string", "max:255"],
            "contact_detail.emergency_contact_phone"    => ["nullable", "string", "max:20"],
            "contact_detail.emergency_contact_relation" => ["nullable", "string", "max:255"],

            "address.current_address"                   => ["required", "string"], // Made required based on blade
            "address.postal_code"                       => ["string", "max:255"],
            "address.village_id"                        => ["required", "integer", Rule::exists(Village::class, 'id')],
        ];
    }

    public $studentId;

    // --- 1. User Info ---
    public $name, $email, $username, $profile_pic;
    public $password, $password_confirmation;

    // --- 2. Academic Info ---
    public $department_id    = "";
    public $program_id       = "";
    public $year_level       = 1;
    public $semester         = "semester_one";
    public $current_semester = 1;
    public $registration_number, $id_card_number, $passport_number;
    public $cgpa, $total_credits_earned;
    public $admission_date, $enrollment_status, $fee_category, $expected_graduation;
    public $academic_status = "active";

    // --- 3. Personal Info ---
    public $date_of_birth;
    public $gender_id = "";
    public $nationality, $blood_group, $disability_details, $previous_education;
    public $has_disability          = false;
    public $has_outstanding_balance = false;

    // --- 4. Contact Info ---
    public $contact_detail = [
        "phone_number"               => "",
        "emergency_contact_name"     => "",
        "emergency_contact_phone"    => "",
        "emergency_contact_relation" => "",
    ];

    // --- 5. Address Info ---
    public $address = [
        "current_address" => "",
        "postal_code"     => "",
        "province_id"     => "",
        "district_id"     => "",
        "commune_id"      => "",
        "village_id"      => "",
    ];

    // --- 6. Dropdown Data ---
    public $departments = [];
    public $programs    = [];
    public $genders     = [];
    public $provinces   = [];
    public $districts   = [];
    public $communes    = [];
    public $villages    = [];

    public function mount($id)
    {
        $this->studentId = $id;

        $student = Student::with([
            'user',
            'address',
            'contactDetail',
            'department',
            'program',
        ])->findOrFail($id);

        $this->loadDropdowns();
        $this->fillUserInfo($student);
        $this->fillAcademicInfo($student);
        $this->fillPersonalInfo($student);
        $this->fillContactInfo($student);
        $this->fillAddressInfo($student);
    }

    private function loadDropdowns()
    {
        $this->departments = Department::active()
            ->select('id', 'name')
            ->get();
        $this->genders = Gender::select('id', 'name')
            ->get();
        $this->provinces = Province::select('id', 'name_kh', 'prov_id')
            ->orderBy('name_kh')
            ->get();
    }

    private function fillUserInfo(Student $student)
    {
        // Guard: Ensure user relationship exists
        if (! $student->user) {
            return;
        }

        $this->name     = $student->user->name;
        $this->email    = $student->user->email;
        $this->username = $student->user->username;
        // profile_pic remains managed by the view/upload system
    }

    private function fillAcademicInfo(Student $student)
    {
        $this->department_id        = $student->department_id;
        $this->program_id           = $student->program_id;
        $this->year_level           = $student->year_level;
        $this->semester             = $student->semester;
        $this->current_semester     = $student->current_semester;
        $this->registration_number  = $student->registration_number;
        $this->id_card_number       = $student->id_card_number;
        $this->passport_number      = $student->passport_number;
        $this->cgpa                 = $student->cgpa;
        $this->total_credits_earned = $student->total_credits_earned;
        $this->enrollment_status    = $student->enrollment_status;
        $this->fee_category         = $student->fee_category;
        $this->academic_status      = $student->academic_status;

        // Date Formatting for <input type="date">
        $this->admission_date = $student->admission_date
            ? Carbon::parse($student->admission_date)->format('Y-m-d')
            : null;

        $this->expected_graduation = $student->expected_graduation
            ? Carbon::parse($student->expected_graduation)->format('Y-m-d')
            : null;

        // Load specific programs if department is selected
        if ($this->department_id) {
            $this->programs = Program::query()
                ->whereHas('major', fn($q) => $q->where(
                    'department_id',
                    $this->department_id
                ))
                ->select('id', 'name')
                ->get();
        }
    }

    private function fillPersonalInfo(Student $student)
    {
        $this->date_of_birth = $student->date_of_birth
            ? Carbon::parse($student->date_of_birth)->format('Y-m-d')
            : null;

        $this->gender_id               = $student->gender_id;
        $this->nationality             = $student->nationality;
        $this->blood_group             = $student->blood_group;
        $this->has_disability          = (bool) $student->has_disability;
        $this->has_outstanding_balance = (bool) $student->has_outstanding_balance;
        $this->disability_details      = $student->disability_details;
        $this->previous_education      = $student->previous_education;
    }

    private function fillContactInfo(Student $student)
    {
        if (! $student->contactDetail) {
            return;
        }

        $phoneNum  = $student->contactDetail->phone_number;
        $contName  = $student->contactDetail->emergency_contact_name;
        $contPhone = $student->contactDetail->emergency_contact_phone;
        $contRelat = $student->contactDetail->emergency_contact_relation;

        $this->contact_detail = [
            "phone_number"               => $phoneNum,
            "emergency_contact_name"     => $contName,
            "emergency_contact_phone"    => $contPhone,
            "emergency_contact_relation" => $contRelat,
        ];
    }

    private function fillAddressInfo(Student $student)
    {
        // 1. Safety Check
        if (! $student->address) {
            return;
        }

        // 2. Fill basic text fields
        $this->address["current_address"] = $student->address->current_address;
        $this->address["postal_code"]     = $student->address->postal_code;

        // 3. Set the Village ID (This is the only ID we have in the DB)
        $this->address["village_id"] = $student->address->village_id;

        // If no village is saved, stop here.
        if (! $this->address["village_id"]) {
            return;
        }

        // --- STEP A: Find the Village & Parent Commune ---
        $village = Village::find($this->address["village_id"]);

        if (! $village) {
            return;
        }

        // Find the Commune using the code stored in the village record
        // usually 'commune_id' in villages table refers to communes.comm_id
        $commune = Commune::where('comm_id', $village->commune_id)->first();

        if ($commune) {
            // Set the dropdown value (ID)
            $this->address["commune_id"] = $commune->id;

            // Load the list of villages for this commune
            $this->villages = Village::where('commune_id', $commune->comm_id)
                ->orWhere('commune_id', (int) $commune->comm_id)
                ->orderBy('name_kh')
                ->get();
        } else {
            return; // Chain broken
        }

        // --- STEP B: Find the Parent District ---
        $district = District::where('dist_id', $commune->district_id)->first();

        if ($district) {
            // Set the dropdown value (ID)
            $this->address["district_id"] = $district->id;

            // Load the list of communes for this district
            $this->communes = Commune::where('district_id', $district->dist_id)
                ->orWhere('district_id', (int) $district->dist_id)
                ->orderBy('name_kh')
                ->get();
        } else {
            return; // Chain broken
        }

        // --- STEP C: Find the Parent Province ---
        $province = Province::where('prov_id', $district->province_id)->first();

        if ($province) {
            // Set the dropdown value (ID)
            $this->address["province_id"] = $province->id;

            // Load the list of districts for this province
            $this->districts = District::where('province_id', $province->prov_id)
                ->orWhere('province_id', (int) $province->prov_id)
                ->orderBy('name_kh')
                ->get();
        }
    }

    // --- Events ---

    public function updatedDepartmentId($value)
    {
        $this->program_id = '';
        $this->programs   = [];

        if (! $value) {
            return;
        }

        $this->programs = Program::query()
            ->whereHas('major', fn($q) => $q->where('department_id', $value))
            ->select('id', 'name')
            ->get();
    }

    public function updatedAddressProvinceId($value)
    {
        $this->address["district_id"] = "";
        $this->address["commune_id"]  = "";
        $this->address["village_id"]  = "";
        $this->districts              = [];
        $this->communes               = [];
        $this->villages               = [];

        if (! $value) {
            return;
        }

        $province = Province::find($value);
        if (! $province) {
            return;
        }

        $this->districts = District::where("province_id", $province->prov_id)
            ->select("id", "name_kh", "dist_id")
            ->orderBy("name_kh")
            ->get();
    }

    public function updatedAddressDistrictId($value)
    {
        $this->address["commune_id"] = "";
        $this->address["village_id"] = "";
        $this->communes              = [];
        $this->villages              = [];

        if (! $value) {
            return;
        }

        $district = District::find($value);
        if (! $district) {
            return;
        }

        $this->communes = Commune::where("district_id", $district->dist_id)
            ->select("id", "name_kh", "comm_id")
            ->orderBy("name_kh")
            ->get();
    }

    public function updatedAddressCommuneId($value)
    {
        $this->address["village_id"] = "";
        $this->villages              = [];

        if (! $value) {
            return;
        }

        $commune = Commune::find($value);
        if (! $commune) {
            return;
        }

        $this->villages = Village::where("commune_id", $commune->comm_id)
            ->select("id", "name_kh", "vill_id")
            ->orderBy("name_kh")
            ->get();
    }

    public function save(StudentService $service)
    {
        $validatedData = $this->validate();

        $student = Student::findOrFail($this->studentId);

        try {
            $service->updateStudent(
                $student,
                $validatedData,
                $this->profile_pic
            );

            return redirect()->route("admin.students.index")
                ->with("success", "Student updated successfully.");

        } catch (\Exception $e) {
            Log::error("Student Update Error: " . $e->getMessage());
            $this->addError(""
                . "system_error", "Failed to update student: "
                . $e->getMessage()
            );
        }
    }

    public function render()
    {
        $student = Student::with(['user', 'department', 'program'])
            ->find($this->studentId);

        return view("livewire.admin.students.edit-student", [
            'student' => $student,
        ]);
    }
}
