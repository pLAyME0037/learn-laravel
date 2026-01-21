<?php
namespace App\Exports;

use App\Models\Attendance;
use App\Models\ClassSession;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings; // For styling
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $classSessionId;
    protected $semesterId;         // New: We need the whole semester context
    protected $allDatesInSemester; // All dates between semester start/end
    protected $attendanceRecords;  // All attendance for this class/semester

    public function __construct($classSessionId, $semesterId)
    {
        $this->classSessionId = $classSessionId;
        $this->semesterId     = $semesterId;

        $classSession = ClassSession::find($this->classSessionId);
        if (! $classSession || ! $classSession->semester) {
            return;
        }

        $semesterStartDate = Carbon::parse($classSession->semester->start_date);
        $semesterEndDate   = Carbon::parse($classSession->semester->end_date);

        $targetDayMap = [
            'Sun' => Carbon::SUNDAY,
            'Mon' => Carbon::MONDAY,
            'Tue' => Carbon::TUESDAY,
            'Wed' => Carbon::WEDNESDAY,
            'Thu' => Carbon::THURSDAY,
            'Fri' => Carbon::FRIDAY,
            'Sat' => Carbon::SATURDAY,
        ];

        $classDayInt = $targetDayMap[$classSession->day_of_week] ?? Carbon::MONDAY;

        $this->allDatesInSemester = collect();
        $currentDate              = $semesterStartDate->copy();

        if ($currentDate->dayOfWeek !== $classDayInt) {
            $currentDate->next($classDayInt);
        }

        while ($currentDate->lte($semesterEndDate)) {
            $this->allDatesInSemester->push($currentDate->format('Y-m-d'));
            $currentDate->addWeek();
        }

        $this->attendanceRecords = Attendance::query()
            ->whereIn('enrollment_id', $classSession->enrollments->pluck('id'))
            ->whereBetween('date', [$semesterStartDate, $semesterEndDate])
            ->get()
            ->groupBy(fn($att) => $att->enrollment_id . '_' . $att->date->format('Y-m-d'));
    }

    public function collection()
    {
        return ClassSession::find($this->classSessionId)->enrollments;
    }

    public function headings(): array
    {
        $dateHeadings = $this->allDatesInSemester
            ->map(fn($date) => Carbon::parse($date)->format('M d')); // e.g. "Jan 01"

        return array_merge(
            ['Student ID', 'Student Name'],
            $dateHeadings->toArray(),
            ['Remarks']// Remarks column at the end
        );
    }

    public function map($enrollment): array
    {
        $rowData = [
            $enrollment->student->student_id,
            $enrollment->student->user->name,
        ];

        foreach ($this->allDatesInSemester as $date) {
            $key        = $enrollment->id . '_' . $date;
            $attendance = $this->attendanceRecords->get($key);

            if ($attendance) {
                $status = $attendance->first()->status;
            } else {
                $status = Carbon::parse($date)->isFuture() ? '' : 'unmarked';
            }
            $rowData[] = $status;
        }

        $rowData[] = ''; // Empty remarks column for each student for editing
        return $rowData;
    }

    public function styles(Worksheet $sheet)
    {
        // Style Header
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')
            ->applyFromArray([
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color'    => ['argb' => 'FFE0E7FD'], // Light Indigo
                ],
            ]);

        // Auto-size columns
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}
