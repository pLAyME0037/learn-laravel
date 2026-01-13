<?php
namespace App\Livewire\Academic;

use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Browsershot\Browsershot;

// Ensure you have this package installed

class TranscriptViewer extends Component
{
    public $student;
    public $transcriptData = [];

    public function mount()
    {
        $this->student = Student::with([
            'user',
            'program.major.department',
            'program.degree',
        ])
            ->where('user_id', auth()->id())
            ->first();

        if (! $this->student) {
            abort(403, 'Student profile not found.');
        }

        // Fetch History Grouped by Semester
        $enrollments = $this->student->enrollments()
            ->with(['classSession.course', 'classSession.semester'])
            ->whereIn('status', ['completed', 'failed']) // Only show finalized grades
            ->get()
            ->sortBy('classSession.semester.start_date');

        // Grouping Logic
        foreach ($enrollments as $record) {
            $semName = $record->classSession->semester->name;
            $this->transcriptData[$semName][] = $record;
        }
    }

    public function downloadPdf()
    {
        // 1. Read the Font File and Convert to Base64
        $fontPath = public_path('fonts/Battambang-Regular.ttf');

        // Check if file exists to prevent crash
        if (! file_exists($fontPath)) {
            abort(500, "Font file not found at: $fontPath");
        }

        $fontData = base64_encode(file_get_contents($fontPath));
        $fontSrc  = 'data:font/truetype;charset=utf-8;base64,' . $fontData;

        // 2. Render HTML with the font data
        $html = view('pdf.transcript', [
            'student'        => $this->student,
            'transcriptData' => $this->transcriptData,
            'generatedAt'    => now()->format('d M Y'),
            'fontSrc'        => $fontSrc, // <--- Pass this to the view
        ])->render();

        // 3. Generate PDF using Browsershot
        return response()->streamDownload(function () use ($html) {
            echo Browsershot::html($html)
                ->format('A4')
                ->margins(10, 10, 10, 10)
                ->newHeadless() // Crucial for new Puppeteer versions
                ->pdf();
        }, 'transcript-' . $this->student->student_id . '.pdf');
    }

    #[Layout('layouts.app', ['header' => 'Academic Transcript'])]
    public function render()
    {
        return view('livewire.academic.transcript-viewer');
    }
}
