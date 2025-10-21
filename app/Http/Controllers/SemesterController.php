<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    public function index()
    {
        $semesters = Semester::with('academicYear')->get();
        $academicYears = AcademicYear::all();
        return view('semesters.index', compact('semesters', 'academicYears'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'boolean',
        ]);

        Semester::create($request->all());

        return redirect()->route('semesters.index')->with('success', 'Semester created successfully.');
    }

    public function update(Request $request, Semester $semester)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'boolean',
        ]);

        $semester->update($request->all());

        return redirect()->route('semesters.index')->with('success', 'Semester updated successfully.');
    }

    public function destroy(Semester $semester)
    {
        $semester->delete();
        return redirect()->route('semesters.index')->with('success', 'Semester deleted successfully.');
    }
}
