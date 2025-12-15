<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display list of students.
     * Logic handled by Livewire Table component.
     */
    public function index()
    {
        return view('admin.students.index');
    }

    /**
     * Show form to create new student.
     * Logic handled by Livewire Form component.
     */
    public function create()
    {
        return view('admin.students.create');
    }

    /**
     * Display specific student profile (ReadOnly).
     */
    public function show(Student $student)
    {
        $student->load(['user', 'program.major', 'address', 'contactDetail', 'enrollments']);
        return view('admin.students.show', compact('student'));
    }

    /**
     * Show form to edit student.
     */
    public function edit(Student $student)
    {
        return view('admin.students.edit', compact('student'));
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