<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CreditScore;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CreditScoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $creditScores = CreditScore::with(['student', 'course'])->paginate(10);
        return view('admin.credit_scores.index', compact('creditScores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $students = Student::all();
        $courses = Course::all();
        $academicYears = \App\Models\AcademicYear::all();
        $semesters = \App\Models\Semester::all();
        return view('admin.credit_scores.create', compact('students', 'courses', 'academicYears', 'semesters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'score' => 'required|integer|min:0|max:100',
            'grade_letter' => 'nullable|string|max:5',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
        ]);

        CreditScore::create($validated);

        return redirect()->route('admin.credit_scores.index')->with('success', 'Credit score created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CreditScore $creditScore): View
    {
        $creditScore->load(['student', 'course']);
        return view('admin.credit_scores.show', compact('creditScore'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CreditScore $creditScore): View
    {
        $students = Student::all();
        $courses = Course::all();
        $academicYears = \App\Models\AcademicYear::all();
        $semesters = \App\Models\Semester::all();
        return view('admin.credit_scores.edit', compact('creditScore', 'students', 'courses', 'academicYears', 'semesters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CreditScore $creditScore): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'score' => 'required|integer|min:0|max:100',
            'grade_letter' => 'nullable|string|max:5',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
        ]);

        $creditScore->update($validated);

        return redirect()->route('admin.credit_scores.show', $creditScore)->with('success', 'Credit score updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CreditScore $creditScore): RedirectResponse
    {
        $creditScore->delete();

        return redirect()->route('admin.credit_scores.index')->with('success', 'Credit score deleted successfully.');
    }
}
