<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $academicYears = AcademicYear::orderBy('is_current', 'desc')->orderBy('start_date', 'desc')->paginate(4);
        return view('admin.academic_years.index', compact('academicYears'));
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicYear $academicYear): View
    {
        $academicYear->load([
            'semesters' => function ($query) {
                $query->with([
                    'courses.department',
                    'courses.classSchedules' => function ($q) {
                        $q->withCount('enrollments');
                    },
                ]);
            },
        ]);

        $departments = Department::all();

        return view('admin.academic_years.show', compact('academicYear', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.academic_years.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'       => 'required|string|unique:academic_years,name',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
            'is_current' => 'boolean',
        ]);

        AcademicYear::create($request->all());

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Academic Year created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicYear $academicYear): View
    {
        return view('admin.academic_years.edit', compact('academicYear'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcademicYear $academicYear): RedirectResponse
    {
        $request->validate([
            'name'       => 'required|string|unique:academic_years,name,' . $academicYear->id,
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
            'is_current' => 'boolean',
        ]);

        $academicYear->update($request->all());

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Academic Year updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        $academicYear->delete();
        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Academic Year deleted successfully.');
    }
}
