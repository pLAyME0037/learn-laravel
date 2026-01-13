<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    /**
     * Display specific student profile (ReadOnly).
     */
    public function show(Student $student)
    {
        $student->load(['user', 'program.major', 'address', 'contactDetail', 'enrollments']);
        return view('admin.students.show', compact('student'));
    }

    /**
     * Generate ID Card PDF (Example Action).
     */
    public function printIdCard(Student $student)
    {
        // Logic to generate PDF using DomPDF
        // $pdf = Pdf::loadView('pdf.id-card', ['student' => $student]);
        // return $pdf->download('id-card.pdf');
    }
}
