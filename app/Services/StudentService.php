<?php

declare (strict_types = 1);

namespace App\Services;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr; // Import Arr helper
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class StudentService
{
    public function registerStudent(array $data, ?UploadedFile $profilePic = null): Student
    {
        return DB::transaction(function () use ($data, $profilePic) {

            // 1. Create User
            $user = $this->createUser($data, $profilePic);

            // 2. Generate ID
            $studentId = $this->generateUniqueStudentId((int) $data['department_id']);

            // 3. Create Student
            // Note: We use Arr::get to safely retrieve values even if keys are missing
            $student = Student::create([
                'user_id'                 => $user->id,
                'student_id'              => $studentId,
                'department_id'           => $data['department_id'],
                'program_id'              => $data['program_id'],
                'year_level'              => $data['year_level'] ?? 1, // Default to 1
                'registration_number'     => $data['registration_number'] ?? null,
                'date_of_birth'           => $data['date_of_birth'],
                'gender_id'               => $data['gender_id'],
                'nationality'             => $data['nationality'],
                'id_card_number'          => $data['id_card_number'] ?? null,
                'passport_number'         => $data['passport_number'] ?? null,
                'admission_date'          => $data['admission_date'],
                'expected_graduation'     => $data['expected_graduation'],
                'semester'                => $data['semester'] ?? 'semester_one', // Align with StoreStudentRequest preprocessing
                'cgpa'                    => 0.00, // Default for new students
                'total_credits_earned'    => 0,
                'academic_status'         => 'active',
                'enrollment_status'       => $data['enrollment_status'],
                'fee_category'            => $data['fee_category'],
                'has_outstanding_balance' => false,
                'previous_education'      => $data['previous_education'] ?? null,
                'blood_group'             => $data['blood_group'] ?? null,
                'has_disability'          => isset($data['has_disability']), // Checkbox check
                'disability_details'      => $data['disability_details'] ?? null,
                'metadata'                => null,
            ]);

            // 4. Create Contact Details
            // $student->contactDetail() will automatically set student_id/contactable_id
            if (isset($data['contact_detail'])) {
                $student->contactDetail()->create([
                    // 'user_id' => $user->id, // REMOVED: Rely on the relationship
                    'phone_number'               => $data['contact_detail']['phone_number'] ?? null,
                    'emergency_contact_name'     => $data['contact_detail']['emergency_contact_name'] ?? null,
                    'emergency_contact_phone'    => $data['contact_detail']['emergency_contact_phone'] ?? null,
                    'emergency_contact_relation' => $data['contact_detail']['emergency_contact_relation'] ?? null,
                ]);
            }

            // 5. Create Address
            if (isset($data['address'])) {
                $student->address()->create([
                    'current_address'   => $data['address']['current_address'] ?? null,
                    'postal_code'       => $data['address']['postal_code'] ?? null,
                    'village_id'        => $data['address']['village_id'] ?? null,
                ]);
            }

            return $student;
        });
    }

    /**
     * Update an existing student record.
     *
     * @param Student $student
     * @param array $data
     * @param UploadedFile|null $profilePic
     * @return Student
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateStudent(Student $student, array $data, ?UploadedFile $profilePic = null): Student
    {
        return DB::transaction(function () use ($student, $data, $profilePic) {
            
            // 1. Update User
            $userData = Arr::only($data, ['name', 'email', 'username']);
            
            // Only hash password if a new one was entered
            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            if ($profilePic) {
                $userData['profile_pic'] = $profilePic->store('profile-pictures', 'public');
            }

            $student->user->update($userData);

            // 2. Update Student
            // Exclude non-student table fields
            $studentData = Arr::except($data, [
                'name', 'email', 'username', 'password', 'password_confirmation', 
                'contact_detail', 'address', 'profile_pic'
            ]);

            $student->update($studentData);

            // 3. Update Relationships
            if (isset($data['contact_detail'])) {
                $student->contactDetail()->updateOrCreate(
                    ['student_id' => $student->id], // Condition
                    $data['contact_detail']         // Values
                );
            }

            if (isset($data['address'])) {
                $student->address()->updateOrCreate(
                    ['student_id' => $student->id], // Condition
                    $data['address']                // Values
                );
            }

            return $student;
        });
    }

    private function createUser(array $data, ?UploadedFile $profilePic): User
    {
        $userData = [
            'name'      => $data['name'],
            'email'     => $data['email'],
            'username'  => $data['username'],
            'password'  => Hash::make($data['password']),
            'is_active' => true,
        ];

        if ($profilePic) {
            $userData['profile_pic'] = $profilePic->store('profile-pictures', 'public');
        }

        $user = User::create($userData);
        $user->assignRole('student');

        return $user;
    }

    private function generateUniqueStudentId(int $departmentId): string
    {
        $maxRetries = 5;
        $attempt    = 0;

        do {
            $id     = (new Student)->generateStudentId($departmentId);
            $exists = Student::where('student_id', $id)->exists();
            $attempt++;
        } while ($exists && $attempt < $maxRetries);

        if ($exists) {
            throw new \Exception("Failed to generate a unique Student ID. System is busy.");
        }

        return $id;
    }
}
