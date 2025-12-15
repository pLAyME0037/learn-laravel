<?php

declare (strict_types = 1);

namespace App\Services;

use App\Models\ContactDetail;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentService
{
    public function registerStudent(
        array $userData,
        array $studentData,
        array $addressData,
        array $contactData,
        ?UploadedFile $profilePic = null
    ): Student {
        return DB::transaction(function () use (
            $userData,
            $studentData,
            $addressData,
            $contactData,
            $profilePic,
        ) {
            // 1. Create User
            $user = $this->createUser($userData, $profilePic);

            // Find dept from prog (Needed for ID Generation)
            $program = Program::with('major')
                ->find($studentData['program_id']);
            $departmentId = $program?->major?->department_id ?? 0;

            // 2. Generate ID
            $studentId = $this->generateUniqueStudentId((int) $departmentId);

            // 3. Create Student
            $__student = array_merge($studentData, [
                'user_id'                 => $user->id,
                'student_id'              => $studentId,
                'cgpa'                    => 0.00,
                'total_credits_earned'    => 0,
                'has_outstanding_balance' => false,
                'current_term'            => $studentData['current_term'] ?? 1,
            ]);

            $student = Student::create($__student);

            // Create Address
            if (empty($contactData)) {
                return;
            }
            $student->contactDetail()->create([
                'phone'              => $contactData['phone'] ?? null,
                'emergency_name'     => $contactData['emergency_name'] ?? null,
                'emergency_phone'    => $contactData['emergency_phone'] ?? null,
                'emergency_relation' => $contactData['emergency_relation'] ?? null,
            ]);

            if (empty($addressData)) {
                return;
            }
            $student->address()->create([
                'current_address' => $addressData['current_address'] ?? null,
                'postal_code'     => $addressData['postal_code'] ?? null,
                'village_id'      => $addressData['village_id'] ?? null,
            ]);

            return $student;
        });
    }

    /**
     * Update an existing student record.
     */
    public function updateStudent(
        Student $student,
        array $userData,
        array $studentData,
        array $addressData,
        array $contactData,
        ?UploadedFile $profilePic = null
    ): Student {
        return DB::transaction(function () use (
            $student,
            $userData,
            $studentData,
            $addressData,
            $contactData,
            $profilePic,
        ) {

            // 1. Update User
            $updataUserData = Arr::only($userData, [
                'name', 'email', 'username',
            ]);

            if (! empty($userData['password'])) {
                $updataUserData['password'] = Hash::make($userData['password']);
            }

            if ($profilePic) {
                $updataUserData['profile_pic'] = $profilePic->store('profile-pictures', 'public');
            }

            $student->user->update($updataUserData);
            $cleanStudentData = Arr::except($studentData, ['student_id']);
            $student->update($cleanStudentData);

            if (! empty($contactData)) {
                $this->handleContactUpdate($student, $contactData);
            }
            if (! empty($addressData)) {
                $student->address()->updateOrCreate([], [
                    'current_address' => $addressData['current_address'] ?? null,
                    'postal_code'     => $addressData['postal_code'] ?? null,
                    'village_id'      => $addressData['village_id'] ?? null,

                ]);
            }

            return $student;
        });
    }

    /**
     * Handle Contact Update with Self-Healing logic using Early Returns.
     */
    private function handleContactUpdate(Student $student, array $data): void
    {
        $student->contactDetail()->updateOrCreate([
            'phone'           => $data['phone'] ?? null,
            'emergency_name'  => $data['emergency_name'] ?? null,
            'emergency_phone' => $data['emergency_phone'] ?? null,
        ]);
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
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('student');
        }

        return $user;
    }

    private function generateUniqueStudentId(int $departmentId): string
    {
        // 1. Get Dept Code
        $department = \App\Models\Department::find($departmentId);
        $code       = $department ? $department->code : 'GEN';

        $year   = now()->format('y'); // e.g. "25"
        $prefix = $code . $year;      // e.g. "CS25"

        // 2. Find the highest ID that starts with this prefix
        $lastRecord = \App\Models\Student::where('student_id', 'LIKE', "{$prefix}%")
            ->orderBy('student_id', 'desc')
            ->first();

        // 3. Determine next sequence
        if ($lastRecord) {
            // Extract the last 4 digits
            // ID: CS250042 -> substr returns "0042" -> intval returns 42
            $lastSequence = intval(substr($lastRecord->student_id, strlen($prefix)));
            $sequence     = $lastSequence + 1;
        } else {
            $sequence = 1;
        }

        // 4. Format
        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    public function calculateCGPA(Student $student)
    {
        $completed = $student->enrollments()
            ->where('status', 'completed')
            ->whereNotNull('grade_points')
            ->get();

        if ($completed->isEmpty()) {
            return 0;
        }

        $totalPoints  = 0;
        $totalCredits = 0;

        foreach ($completed as $record) {
            $credits = $record->classSession->course->credits;
            $totalPoints += ($record->grade_points * $credits);
            $totalCredits += $credits;
        }

        $cgpa = $totalCredits > 0 ? ($totalPoints / $totalCredits) : 0;

        $student->update(['cgpa' => round($cgpa, 2)]);
    }
}
