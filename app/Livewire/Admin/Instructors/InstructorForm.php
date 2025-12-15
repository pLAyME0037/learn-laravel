<?php
namespace App\Livewire\Admin\Instructors;

use App\Models\Department;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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
        'staff_id'      => '',
        'department_id' => '',
        'payscale'      => 1,
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
        $this->departments = Department::pluck('name', 'id');

        if ($instructorId) {
            $this->isEdit     = true;
            $this->instructor = Instructor::with(['user', 'address', 'contactDetail'])->find($instructorId);

            // Hydrate
            $this->user['name']             = $this->instructor->user->name;
            $this->user['username']         = $this->instructor->user->username;
            $this->user['email']            = $this->instructor->user->email;
            $this->profile['staff_id']      = $this->instructor->staff_id;
            $this->profile['department_id'] = $this->instructor->department_id;
            // $this->profile['payscale'] = $this->instructor->payscale; // If you use this

            if ($this->instructor->address) {
                $this->address = $this->instructor->address->only(['current_address', 'postal_code', 'village_id']);
            }
            if ($this->instructor->contactDetail) {
                $this->contact = $this->instructor->contactDetail->only(['phone', 'emergency_name', 'emergency_phone']);
            }
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
            'user.name'             => 'required|string|max:255',
            'user.email'            => 'required|email|unique:users,email' . ($this->isEdit ? ',' . $this->instructor->user_id : ''),
            'user.username' => 'required|string|max:50|unique:users,username' . ($this->isEdit ? ',' . $this->instructor->user_id : ''),
            'profile.department_id' => 'required|exists:departments,id',
            'profile.staff_id'      => 'required|string|unique:instructors,staff_id' . ($this->isEdit ? ',' . $this->instructor->id : ''),
            'contact.phone'         => 'required',
        ];

        if (! $this->isEdit) {
            $rules['user.password'] = 'required|min:8|confirmed';
        }

        $this->validate($rules);

        // Logic here (Or extract to InstructorService)
        // For brevity, I'll put basic logic here, but you should use a Service like StudentService
        DB::transaction(function () {
            // 1. User
            if ($this->isEdit) {
                $this->instructor->user->update([
                    'name'  => $this->user['name'],
                    'email' => $this->user['email'],
                    'username' => $this->user['username'],
                ]);
                $user = $this->instructor->user;
            } else {
                $user = User::create([
                    'name'      => $this->user['name'],
                    'email'     => $this->user['email'],
                    'username' => $this->user['username'],
                    'password'  => bcrypt($this->user['password']),
                    'is_active' => true,
                ]);
                $user->assignRole('staff'); // Spatie Role
            }

            // 2. Instructor Profile
            $instructorData = [
                'department_id' => $this->profile['department_id'],
                'staff_id'      => $this->profile['staff_id'],
            ];

            if ($this->isEdit) {
                $this->instructor->update($instructorData);
                $instructor = $this->instructor;
            } else {
                $instructor = Instructor::create(array_merge(['user_id' => $user->id], $instructorData));
            }

            // 3. Polymorphic Relations
            $instructor->contactDetail()->updateOrCreate([], $this->contact);
            $instructor->address()->updateOrCreate([], $this->address);
        });

        session()->flash('success', 'Instructor saved successfully.');
        return redirect()->route('admin.instructors.index');
    }

    #[Layout('layouts.app', ['header' => 'Instructor Management'])]
    public function render()
    {
        return view('livewire.admin.instructors.instructor-form');
    }
}
