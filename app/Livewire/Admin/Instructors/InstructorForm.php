<?php
namespace App\Livewire\Admin\Instructors;

use App\Models\Department;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

class InstructorForm extends Component
{
    public ?Instructor $instructor = null;
    public $isEdit                 = false;

    // Form Data
    public $user = [
        'name'             => '',
        'username'         => '',
        'email'            => '',
        'password'         => '',
        'confirm_password' => '',
    ];
    public $profile = [
        'staff_id'       => '', // should be auto-generate
        'department_id'  => '',
        // dynamic attribute
        'specialization' => '',
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

    // Data Sources
    public $departments = [];

    public function mount($instructorId = null)
    {
        // dd($this->instructor->toArray(), $this->user, $this->contact, $this->address);
        $this->departments = Department::pluck('name', 'id');

        if ($instructorId) {
            $this->isEdit     = true;
            $this->instructor = Instructor::with([
                'user',
                'address',
                'contactDetail',
            ])->find($instructorId);

            // Hydrate
            $this->user['name']             = $this->instructor->user->name;
            $this->user['username']         = $this->instructor->user->username;
            $this->user['email']            = $this->instructor->user->email;
            $this->profile['staff_id']      = $this->instructor->staff_id;
            $this->profile['department_id'] = $this->instructor->department_id;

            // Load json attribute
            $attr                            = $this->instructor->attributes ?? [];
            $this->profile['specialization'] = $attr['specialization'] ?? '';

            if ($this->instructor->address) {
                $this->address = $this->instructor->address->only(['current_address', 'postal_code', 'village_id']);
            }
            if ($this->instructor->contactDetail) {
                $this->contact = $this->instructor->contactDetail->only(['phone', 'emergency_name', 'emergency_phone']);
            }
        }

        if (! $instructorId) {
            $this->profile['staff_id'] = "STF-" . now()->year . '-' . str_pad((string) rand(1, 9999), 4, "0", STR_PAD_LEFT);
        }
    }

    /**
     * Action: Auto-generate username from Name or Email
     */
    public function generateUsername()
    {
        $base = '';

        if (! empty($this->user['email'])) {
            $base = explode('@', $this->user['email'])[0];
        } elseif (! empty($this->user['name'])) {
            $base = strtolower(str_replace(' ', '.', $this->user['name']));
        } else {
            $base = 'staff' . rand(100, 999);
        }

        // Ensure uniqueness by appending random number if taken
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

    public function save()
    {
        $rules = [
            'user.name'              => 'required|string|max:255',
            'user.email'             => [
                'required',
                'email',
                Rule::unique('users', 'email')
                    ->ignore($this->isEdit ? $this->instructor->user_id : null),
            ],
            'user.username'          => [
                'required',
                'string',
                'max:50', Rule::unique('users', 'username')
                    ->ignore($this->isEdit ? $this->instructor->user_id : null),
            ],
            'profile.department_id'  => 'required|exists:departments,id',
            'profile.staff_id'       => [
                'nullable',
                'string',
                Rule::unique('instructors', 'staff_id')
                    ->ignore($this->isEdit ? $this->instructor->id : null),
            ],
            'profile.specialization' => 'required|string|max:125',
            'profile.office_hours'   => 'nullable',
            'contact.phone'          => 'required',
        ];

        if (! $this->isEdit) {
            $rules['user.password'] = 'required|min:8|confirmed';
        }

        $this->validate($rules);

        DB::transaction(function () {
            // 1. User
            if ($this->isEdit) {
                $userData = [
                    'name'     => $this->user['name'],
                    'email'    => $this->user['email'],
                    'username' => $this->user['username'],
                ];
                $this->instructor->user->update($userData);
                if (! empty($this->user['password'])) {
                    $userData['password'] = bcrypt($this->user['password']);
                }
                $user = $this->instructor->user;
            } else {
                $user = User::create([
                    'name'      => $this->user['name'],
                    'email'     => $this->user['email'],
                    'username'  => $this->user['username'],
                    'password'  => bcrypt($this->user['password']),
                    'is_active' => true,
                ]);
                $user->assignRole('instructor'); // Spatie Role
            }

            // 2. Instructor Profile
            $attribute = [
                'specialization' => $this->profile['specialization'],
                // 'office_hours' => $this->profile['office_hours'],
            ];

            $staffId = $this->profile['staff_id'];
            if (empty($staffId)) {
                $staffId = "STF-" . now()->year . '-' . str_pad((string) rand(1, 9999), 4, "0", STR_PAD_LEFT);
            }

            $instructorData = [
                'department_id' => $this->profile['department_id'],
                'staff_id'      => $staffId,
                'attributes'    => $attribute,
            ];

            if ($this->isEdit) {
                $this->instructor->update($instructorData);
                $instructor = $this->instructor;
            } else {
                $instructor = Instructor::create(array_merge(['user_id' => $user->id], $instructorData));
            }

            // 3. Relations
            $instructor->contactDetail()->updateOrCreate([], [
                'phone'           => $this->contact['phone'],
                'emergency_name'  => $this->contact['emergency_name'] ?? null,
                'emergency_phone' => $this->contact['emergency_phone'] ?? null,
            ]);

            $instructor->address()->updateOrCreate([], [
                'current_address' => $this->address['current_address'] ?? null,
                'postal_code'     => $this->address['postal_code'] ?? null,
                'village_id'      => $this->address['village_id'] ?? null,
            ]);
        });

        return redirect()->route('admin.instructors.index')
            ->with('success', 'Instructor saved successfully.');
    }

    #[Layout('layouts.app', ['header' => 'Instructor Management'])]
    public function render()
    {
        return view('livewire.admin.instructors.instructor-form');
    }
}
