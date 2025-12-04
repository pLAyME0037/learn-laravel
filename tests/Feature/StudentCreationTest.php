<?php
namespace Tests\Feature;

use App\Models\Department;
use App\Models\Gender;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    // Create an admin user and authenticate
    $this->adminRole   = Role::firstOrCreate(['name' => 'admin']);
    $this->studentRole = Role::firstOrCreate(['name' => 'student']); // Ensure student role exists
    $this->admin       = User::factory()->create();
    $this->admin->assignRole($this->adminRole);

    // Ensure permissions exist and assign them to the admin role
    Permission::firstOrCreate(['name' => 'view.students']);
    Permission::firstOrCreate(['name' => 'create.students']);
    $this->adminRole->givePermissionTo(['view.students', 'create.students']);

    $this->actingAs($this->admin);

    // Create necessary related data
    $this->department = Department::factory()->create();
    $this->program    = Program::factory()->create();
    // Create genders once and store them
    $this->genders = [
        'Male'   => Gender::firstOrCreate(['name' => 'Male']),
        'Female' => Gender::firstOrCreate(['name' => 'Female']),
        'Other'  => Gender::firstOrCreate(['name' => 'Other']),
    ];
    $this->gender = $this->genders['Male']; // Default gender for tests
});

test('student creation page can be rendered', function () {
    $response = $this->get(route('admin.students.create'));
    $response->assertStatus(200);
});

test('student can be created with valid data', function () {
    $userData = [
        'name'                  => $this->faker->name,
        'email'                 => $this->faker->unique()->safeEmail,
        'username'              => $this->faker->unique()->slug(2),
        'password'              => 'password',
        'password_confirmation' => 'password',
    ];

    $studentData = [
        'department_id'              => $this->department->id,
        'program_id'                 => $this->program->id,
        'date_of_birth'              => $this->faker->date(),
        'gender_id'                  => $this->faker->randomElement(array_values($this->genders))->id,
        'nationality'                => $this->faker->country,
        'phone'                      => $this->faker->phoneNumber,
        'emergency_contact_name'     => $this->faker->name,
        'emergency_contact_phone'    => $this->faker->phoneNumber,
        'emergency_contact_relation' => 'Parent',
        'current_address'            => $this->faker->address,
        'city'                       => $this->faker->city,
        'state'                      => $this->faker->state,
        'country'                    => $this->faker->country,
        'postal_code'                => $this->faker->postcode,
        'admission_date'             => $this->faker->date(),
        'enrollment_status'          => $this->faker->randomElement(['full_time', 'part_time', 'exchange', 'study_abroad']),
        'fee_category'               => $this->faker->randomElement(['regular', 'scholarship', 'financial_aid', 'self_financed']),
        'has_disability'             => $this->faker->boolean,
        'disability_details'         => $this->faker->boolean ? $this->faker->sentence : null,
    ];

    $response = $this->post(route('admin.students.store'), array_merge($userData, $studentData));

    $response->assertRedirect(route('admin.students.index'));
    $response->assertSessionHas('success', 'Student created successfully.');

    $this->assertDatabaseHas('users', [
        'email'    => $userData['email'],
        'username' => $userData['username'],
    ]);

    $user = User::where('email', $userData['email'])->first();

    $this->assertDatabaseHas('students', [
        'user_id'       => $user->id,
        'department_id' => $studentData['department_id'],
        'program_id'    => $studentData['program_id'],
        'gender_id'     => $studentData['gender_id'],
        'nationality'   => $studentData['nationality'],
    ]);

    $student = Student::where('user_id', $user->id)->first();
    $this->assertNotNull($student->student_id);
    $this->assertTrue($user->hasRole('student'));
});

test('student creation fails with invalid data', function () {
    $validUserData = [
        'name'                  => $this->faker->name,
        'email'                 => $this->faker->unique()->safeEmail,
        'username'              => $this->faker->unique()->slug(2),
        'password'              => 'password',
        'password_confirmation' => 'password',
    ];

    $invalidStudentData = [
        'department_id'              => null,               // Should fail 'required' and 'exists'
        'program_id'                 => null,               // Should fail 'required' and 'exists'
        'date_of_birth'              => 'not-a-date',       // Invalid date format
        'gender_id'                  => null,               // Should fail 'required' and 'exists'
        'nationality'                => '',                 // Required
        'phone'                      => '',                 // Required
        'emergency_contact_name'     => '',                 // Required
        'emergency_contact_phone'    => '',                 // Required
        'emergency_contact_relation' => '',                 // Required
        'current_address'            => '',                 // Required
        'city'                       => '',                 // Required
        'state'                      => '',                 // Required
        'country'                    => '',                 // Required
        'postal_code'                => '',                 // Required
        'admission_date'             => 'not-a-date',       // Invalid date format
        'enrollment_status'          => 'invalid_status',   // Invalid enum value
        'fee_category'               => 'invalid_category', // Invalid enum value
        'has_disability'             => 'not-a-boolean',    // Invalid boolean
    ];

    $response = $this->post(route('admin.students.store'), array_merge($validUserData, $invalidStudentData));

    $response->assertStatus(302); // Expect a redirect status

    // Manually check for session errors
    $errors = session()->get('errors')->getBag('default');
    expect($errors->has('department_id'))->toBeTrue();
    expect($errors->has('program_id'))->toBeTrue();
    expect($errors->has('date_of_birth'))->toBeTrue();
    expect($errors->has('gender_id'))->toBeTrue();
    expect($errors->has('nationality'))->toBeTrue();
    expect($errors->has('phone'))->toBeTrue();
    expect($errors->has('emergency_contact_name'))->toBeTrue();
    expect($errors->has('emergency_contact_phone'))->toBeTrue();
    expect($errors->has('emergency_contact_relation'))->toBeTrue();
    expect($errors->has('current_address'))->toBeTrue();
    expect($errors->has('city'))->toBeTrue();
    expect($errors->has('state'))->toBeTrue();
    expect($errors->has('country'))->toBeTrue();
    expect($errors->has('postal_code'))->toBeTrue();
    expect($errors->has('admission_date'))->toBeTrue();
    expect($errors->has('enrollment_status'))->toBeTrue();
    expect($errors->has('fee_category'))->toBeTrue();
    expect($errors->has('has_disability'))->toBeTrue();

    $this->assertDatabaseCount('users', 1); // Only the admin user should exist
    $this->assertDatabaseCount('students', 0);
});

test('unauthenticated user cannot create student', function () {
    $this->post(route('logout')); // Log out the admin user

    $initialUserCount    = User::count();
    $initialStudentCount = Student::count();

    $userData = [
        'name'                  => $this->faker->name,
        'email'                 => $this->faker->unique()->safeEmail,
        'username'              => $this->faker->unique()->userName,
        'password'              => 'password',
        'password_confirmation' => 'password',
    ];

    $studentData = [
        'department_id'              => $this->department->id,
        'program_id'                 => $this->program->id,
        'date_of_birth'              => $this->faker->date(),
        'gender_id'                  => $this->faker->randomElement($this->genders)->id,
        'nationality'                => $this->faker->country,
        'phone'                      => $this->faker->phoneNumber,
        'emergency_contact_name'     => $this->faker->name,
        'emergency_contact_phone'    => $this->faker->phoneNumber,
        'emergency_contact_relation' => 'Parent',
        'current_address'            => $this->faker->address,
        'city'                       => $this->faker->city,
        'state'                      => $this->faker->state,
        'country'                    => $this->faker->country,
        'postal_code'                => $this->faker->postcode,
        'admission_date'             => $this->faker->date(),
        'enrollment_status'          => $this->faker->randomElement(['full_time', 'part_time', 'exchange', 'study_abroad']),
        'fee_category'               => $this->faker->randomElement(['regular', 'scholarship', 'financial_aid', 'self_financed']),
        'has_disability'             => $this->faker->boolean,
        'disability_details'         => $this->faker->boolean ? $this->faker->sentence : null,
    ];

    $response = $this->post(route('admin.students.store'), array_merge($userData, $studentData));

    $response->assertStatus(302); // Redirect to login
    $response->assertRedirect(route('login'));
    $this->assertGuest();
    $this->assertDatabaseCount('users', $initialUserCount);
    $this->assertDatabaseCount('students', $initialStudentCount);
});
