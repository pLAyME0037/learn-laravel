<?php

namespace App\Http\Controllers;

use App\Models\AcademicRecord;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AcademicRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $academicRecords = AcademicRecord::with(['student', 'course', 'semester'])
            ->latest()
            ->paginate(10);
        return view('admin.academic_records.index', compact('academicRecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $students = \App\Models\Student::with('user')->get();
        $courses = \App\Models\Course::all();
        $academicYears = \App\Models\AcademicYear::all();
        $semesters = \App\Models\Semester::all();
        return view('admin.academic_records.create', compact('students', 'courses', 'academicYears', 'semesters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'semester_id' => 'required|exists:semesters,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'grade' => 'nullable|string|max:5',
            'gpa_score' => 'nullable|numeric',
            'status' => 'required|string|max:255',
        ]);

        AcademicRecord::create($validated);

        return redirect()->route('admin.academic_records.index')->with('success', 'Academic record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicRecord $academicRecord): View
    {
        return view('admin.academic_records.show', compact('academicRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicRecord $academicRecord): View
    {
        $students = \App\Models\Student::with('user')->get();
        $courses = \App\Models\Course::all();
        $academicYears = \App\Models\AcademicYear::all();
        $semesters = \App\Models\Semester::all();
        return view('admin.academic_records.edit', compact('academicRecord', 'students', 'courses', 'academicYears', 'semesters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcademicRecord $academicRecord): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'semester_id' => 'required|exists:semesters,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'grade' => 'nullable|string|max:5',
            'gpa_score' => 'nullable|numeric',
            'status' => 'required|string|max:255',
        ]);

        $academicRecord->update($validated);

        return redirect()->route('admin.academic_records.show', $academicRecord)->with('success', 'Academic record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicRecord $academicRecord): RedirectResponse
    {
        $academicRecord->delete();

        return redirect()->route('admin.academic_records.index')->with('success', 'Academic record deleted successfully.');
    }
}
