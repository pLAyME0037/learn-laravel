<?php
namespace App\Livewire\Instructor;

use App\Exports\AttendanceExport;
use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\Dictionary;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceTaker extends Component
{
    use WithFileUploads;

    public $classSession;
    public $date;

    public $sessionDates = [];
    public $selectedDate;

    // Form Data: [enrollment_id => status]
    public $attendance    = [];
    public $statusOptions = [];

    // File Upload
    public $importFile;

    public function mount($classSessionId)
    {
        $this->classSession = ClassSession::with('enrollments.student.user')
            ->with('semester')
            ->findOrFail($classSessionId);

        // Security Check
        if (
            $this->classSession->instructor_id !== auth()->id()
            && ! auth()->user()->hasRole('admin')
        ) {
            abort(403);
        }

        $this->statusOptions = Dictionary::options('attendance_status');
        $this->calculateSessionDates();
        $this->selectedDate = $this->getNearestClassDate();
        $this->loadAttendance();
    }

    public function calculateSessionDates()
    {
        if (! $this->classSession || ! $this->classSession->semester) {
            return;
        }

        $semesterStartDate = Carbon::parse($this->classSession->semester->start_date);
        $semesterEndDate   = Carbon::parse($this->classSession->semester->end_date);

        $targetDayMap = [
            'Sun' => Carbon::SUNDAY,
            'Mon' => Carbon::MONDAY,
            'Tue' => Carbon::TUESDAY,
            'Wed' => Carbon::WEDNESDAY,
            'Thu' => Carbon::THURSDAY,
            'Fri' => Carbon::FRIDAY,
            'Sat' => Carbon::SATURDAY,
        ];

        $targetDay = $targetDayMap[$this->classSession->day_of_week] ?? Carbon::MONDAY;

        $currentDate = $semesterStartDate->copy();

        if ($currentDate->dayOfWeek !== $targetDay) {
            $currentDate->next($targetDay);
        }

        $count = 1;
        while ($currentDate->lte($semesterEndDate) && $count <= 16) {
            $dateStr = $currentDate->format('Y-m-d');
            $label   = "Session {$count}: " . $currentDate->format('d M, Y');

            // Mark furture dates visibility in label
            if ($currentDate->isFuture()) {
                $label .= " (Upcoming)";
            }

            $this->sessionDates[$dateStr] = $label;

            $currentDate->addWeek();
            $count++;
        }
    }

    public function getNearestClassDate()
    {
        $today = Carbon::today()->format('Y-m-d');
        // 1. Is today a class day?
        if (isset($this->sessionDates[$today])) {
            return $today;
        }
        // 2. Find the cloest past date (last session)
        $dates    = array_keys($this->sessionDates);
        $lastDate = null;

        foreach ($dates as $date) {
            if ($date > $today) {
                // Hit tthe furture so return the prevoius one (last session)
                return $lastDate ?? $date;
            }
            $lastDate = $date;
        }
        // if all date are past, return the very last one
        return end($dates);
    }

    public function updatedSelectedDate($v)
    {
        // dd($v);
        $this->date = $this->selectedDate;
        $this->loadAttendance();
    }

    public function loadAttendance()
    {
        // 1. Fetch existing records for this date
        $existing = Attendance::query()
            ->whereIn('enrollment_id', $this->classSession->enrollments->pluck('id'))
            ->whereDate('date', Carbon::parse($this->selectedDate)->format('Y-m-d'))
            ->pluck('status', 'enrollment_id');
        
        // 2. Reset array to avoid stale data
        $this->attendance = []; 

        // 2. Initialize form state
        foreach ($this->classSession->enrollments as $enrollment) {
            // Default to 'present' if no record exists
            $this->attendance[$enrollment->id] = $existing[$enrollment->id] ?? 'present';
        }
        // dd($existing->toArray(), $this->selectedDate);
    }

    public function save()
    {
        foreach ($this->attendance as $enrollmentId => $status) {
            Attendance::updateOrCreate(
                [
                    'enrollment_id' => $enrollmentId,
                    'date'          => Carbon::parse($this->selectedDate)->format('Y-m-d'),
                ],
                [
                    'status' => $status,
                ]
            );
        }

        $this->dispatch('swal:success', [
            'message' => 'Attendance saved successfully.',
        ]);
    }

    public function markAll($status)
    {
        foreach ($this->attendance as $id => $val) {
            $this->attendance[$id] = $status;
        }
    }

    // Excel Logic
    public function exportTemplate()
    {
        // Need to get the semester ID from the class session
        $semesterId = $this->classSession->semester_id;
        $dateStr    = Carbon::now()->format('Y-m-d'); // For filename

        return Excel::download(
            new AttendanceExport($this->classSession->id, $semesterId),
            'attendance_template_' . $dateStr . '.xlsx'
        );
    }

    public function import()
    {
        $this->validate(['importFile' => 'required|file|mimes:xlsx,csv']);

        // Pass classSessionId to the Import class
        $import = new \App\Imports\AttendanceImport($this->classSession->id);
        Excel::import($import, $this->importFile);

        $errors = $import->getErrors();

        if (empty($errors)) {
            $this->dispatch('swal:success', [
                'message' => 'Attendance imported successfully.',
            ]);
        } else {
            $this->dispatch('swal:error', [
                'message' => 'Attendance imported with issues: ' . implode(', ', $errors),
            ]);
        }

        $this->reset('importFile');
        $this->loadAttendance(); // Refresh table
    }

    #[Layout('layouts.app', ['header' => 'Attendance'])]
    public function render()
    {
        // dump($this->selectedDate, $this->attendance);
        return view('livewire.instructor.attendance-taker');
    }
}
