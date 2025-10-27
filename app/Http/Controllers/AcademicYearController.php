<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Course;
use App\Models\Department;
use App\Models\ClassSchedule;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::orderBy('is_current', 'desc')->orderBy('start_date', 'desc')->get();
        return view('academic_years.index', compact('academicYears'));
    }

    public function show(AcademicYear $academicYear)
    {
        $academicYear->load([
            'semesters' => function ($query) {
                $query->with([
                    'courses.department',
                    'courses.classSchedules' => function ($q) {
                        $q->withCount('enrollments');
                    }
                ]);
            }
        ]);

        $departments = Department::all();

        return view('academic_years.show', compact('academicYear', 'departments'));
    }

    public function create()
    {
        return view('academic_years.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:academic_years,name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'boolean',
        ]);

        AcademicYear::create($request->all());

        return redirect()->route('admin.academic_years.index')
            ->with('success', 'Academic Year created successfully.');
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('academic_years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $request->validate([
            'name' => 'required|string|unique:academic_years,name,' . $academicYear->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'boolean',
        ]);

        $academicYear->update($request->all());

        return redirect()->route('admin.academic_years.index')
            ->with('success', 'Academic Year updated successfully.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();
        return redirect()->route('admin.academic_years.index')
            ->with('success', 'Academic Year deleted successfully.');
    }
}
