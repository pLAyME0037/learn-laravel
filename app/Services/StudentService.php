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
            $__userData = [
                'name'      => $userData['name'],
                'email'     => $userData['email'],
                'username'  => $userData['username'],
                'password'  => Hash::make($userData['password']),
                'is_active' => true,
            ];

            if ($profilePic) {
                $__userData['profile_pic'] = $profilePic->store('profile-pictures', 'public');
            }

            // 1. Create User
            $user = User::create($__userData);

            if (method_exists($user, 'assignRole')) {
                $user->assignRole('student');
            }

            // Find dept from prog (Needed for ID Generation)
            $program = Program::with('major')
                ->find($studentData['program_id']);
            $departmentId = $program?->major?->department_id ?? 0;
            // 2. Generate ID
            $studentId = $this->generateUniqueStudentId((int) $departmentId);

            $attributes = [
                'dob'         => $studentData['dob'],
                'gender'      => $studentData['gender'],
                'nationality' => $studentData['nationality'],
                'blood_group' => $studentData['blood_group'],
            ];

            // 3. Create Student
            $__student = array_merge($studentData, [
                'user_id'                 => $user->id,
                'student_id'              => $studentId,
                'program_id'              => $studentData['program_id'],
                'year_level'              => $studentData['year_level'],
                'current_term'            => $studentData['current_term'] ?? 1,
                'has_disability'          => $studentData['has_disability'],
                'disability_details'      => $studentData['disability_details'],
                'academic_status'         => 'active',
                'attributes'              => $attributes,
                'cgpa'                    => 0.00,
                'has_outstanding_balance' => false,
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
            $currentAttr = $student->attributes ?? [];

            $dob         = $studentData['dob'] ?? $currentAttr['dob'] ?? null;
            $gender      = $studentData['gender'] ?? $currentAttr['gender'] ?? null;
            $nationality = $studentData['nationality'] ?? $currentAttr['nationality'] ?? null;
            $blood_group = $studentData['blood_group'] ?? $currentAttr['blood_group'] ?? null;

            $newAttr = array_merge($currentAttr, [
                'dob'         => $dob,
                'gender'      => $gender,
                'nationality' => $nationality,
                'blood_group' => $blood_group,
            ]);

            $studentData['attributes'] = $newAttr;

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

            unset(
                $studentData['dob'],
                $studentData['gender'],
                $studentData['nationality'],
                $studentData['blood_group']
            );
            $student->user->update($updataUserData);
            $cleanStudentData = Arr::except($studentData, ['student_id']);

            $student->update($cleanStudentData);

            if (! empty($contactData)) {
                $student->contactDetail()->updateOrCreate([
                    'phone'           => $contactData['phone'] ?? null,
                    'emergency_name'  => $contactData['emergency_name'] ?? null,
                    'emergency_phone' => $contactData['emergency_phone'] ?? null,
                ]);
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
    
    /**
     * Promote all active students to the next term.
     */
    public function promoteAllStudents()
    {
        return DB::transaction(function () {
            $count = 0;
            
            // Chunking for performance with large datasets
            Student::where('academic_status', 'active')
                ->chunkById(100, function ($students) use (&$count) {
                    foreach ($students as $student) {
                        $newTerm = $student->current_term + 1;
                        
                        if ($newTerm > 8) {
                            $student->update(['academic_status' => 'graduated']);
                        } else {
                            $student->update([
                                'current_term' => $newTerm,
                                'year_level' => ceil($newTerm / 2)
                            ]);
                        }
                        $count++;
                    }
                });
                
            return $count;
        });
    }
}
