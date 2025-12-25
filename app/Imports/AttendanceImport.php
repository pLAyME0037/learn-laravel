<?php
namespace App\Imports;

use App\Models\Attendance;
use App\Models\Enrollment;
use Carbon\Carbon; // Use ToCollection for more control
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AttendanceImport implements ToCollection, WithHeadingRow
{
    protected $classSessionId;
    protected $errors = [];

    public function __construct($classSessionId)
    {
        $this->classSessionId = $classSessionId;
    }

    public function collection(Collection $rows)
    {
        // 1. Get all enrollments for this class once for lookup speed
        $enrollments = Enrollment::where('class_session_id', $this->classSessionId)
            ->with('student')
            ->get()
            ->keyBy(fn($e) => $e->student->student_id); // Map by student_id for easy lookup

        foreach ($rows as $row) {
            $studentId      = $row['student_id'] ?? null;
            $attendanceDate = $row['date'] ?? null;                                                               // Expect 'Date' column
            $status         = strtolower($row['status_presentabsentlateexcused'] ?? $row['status'] ?? 'present'); // Handle different header names

            if (! $studentId || ! $attendanceDate) {
                $this->errors[] = "Row missing Student ID or Date: " . json_encode($row);
                continue;
            }

            $enrollment = $enrollments->get($studentId);

            if ($enrollment) {
                Attendance::updateOrCreate(
                    [
                        'enrollment_id' => $enrollment->id,
                        'date'          => Carbon::parse($attendanceDate)->format('Y-m-d'),
                    ],
                    [
                        'status'  => $status,
                        'remarks' => $row['remarks'] ?? null, // Import remarks too
                    ]
                );
            } else {
                $this->errors[] = "Student ID {$studentId} not found in this class enrollment.";
            }
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
