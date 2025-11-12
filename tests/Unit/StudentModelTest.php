<?php

namespace Tests\Unit;

use App\Models\Department;
use App\Models\Gender;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    // Create genders once and store them
    $this->genders = [
        'Male'   => Gender::firstOrCreate(['name' => 'Male']),
        'Female' => Gender::firstOrCreate(['name' => 'Female']),
        'Other'  => Gender::firstOrCreate(['name' => 'Other']),
    ];
});

test('student attributes can be assigned', function () {
    $studentData = [
        'user_id'             => User::factory()->create()->id,
        'department_id'       => Department::factory()->create()->id,
        'program_id'          => Program::factory()->create()->id,
        'student_id'          => 'GEN250001',
        'registration_number' => 'REG-001',
        'date_of_birth'       => '2000-01-01',
        'gender_id'           => $this->genders['Male']->id, // Use pre-created gender
        'nationality'         => 'Cambodian',
        'phone'               => '012345678',
        'current_address'     => 'Phnom Penh',
        'admission_date'      => '2023-09-01',
        'academic_status'     => 'active',
        'enrollment_status'   => 'full_time',
        'fee_category'        => 'regular',
    ];

    $student = Student::create($studentData);

    expect($student->user_id)->toBe($studentData['user_id']);
    expect($student->department_id)->toBe($studentData['department_id']);
    expect($student->program_id)->toBe($studentData['program_id']);
    expect($student->student_id)->toBe($studentData['student_id']);
    expect($student->date_of_birth->format('Y-m-d'))->toBe($studentData['date_of_birth']);
    expect($student->nationality)->toBe($studentData['nationality']);
    expect($student->phone)->toBe($studentData['phone']);
    expect($student->current_address)->toBe($studentData['current_address']);
    expect($student->admission_date->format('Y-m-d'))->toBe($studentData['admission_date']);
    expect($student->academic_status)->toBe($studentData['academic_status']);
    expect($student->enrollment_status)->toBe($studentData['enrollment_status']);
    expect($student->fee_category)->toBe($studentData['fee_category']);
});

test('cgpa casting works correctly', function () {
    $student = Student::factory()->create(['cgpa' => '3.75']);
    expect($student->cgpa)->toBe('3.75');

    $student->cgpa = '3.999'; // Assign as string to match cast behavior
    $student->save();
    expect($student->fresh()->cgpa)->toBe('4.00'); // Due to decimal:2 casting, it should round and return as string
});

test('generate student id method works', function () {
    $department = Department::factory()->create(['code' => 'CS']);
    $student = new Student();
    $studentId = $student->generateStudentId($department->id);

    // Ensure Gender factory is not called multiple times within this test
    // The Gender::factory() calls in StudentFactory will now use the pre-created genders if they exist

    $year = now()->format('y');
    expect($studentId)->toMatch("/CS{$year}\d{4}/");

    // Test uniqueness
    Student::factory()->count(5)->create(['department_id' => $department->id]);
    $newStudentId = (new Student())->generateStudentId($department->id);
    expect($newStudentId)->not->toBe($studentId);
});

test('student relationships are correctly defined', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();
    $program = Program::factory()->create();
    $gender = $this->genders['Female']; // Use a different pre-created gender for this test

    $student = Student::factory()->create([
        'user_id'       => $user->id,
        'department_id' => $department->id,
        'program_id'    => $program->id,
        'gender_id'     => $gender->id,
    ]);

    expect($student->user)->toBeInstanceOf(User::class);
    expect($student->user->id)->toBe($user->id);

    expect($student->department)->toBeInstanceOf(Department::class);
    expect($student->department->id)->toBe($department->id);

    expect($student->program)->toBeInstanceOf(Program::class);
    expect($student->program->id)->toBe($program->id);

    expect($student->gender)->toBeInstanceOf(Gender::class);
    expect($student->gender->id)->toBe($gender->id);
});
