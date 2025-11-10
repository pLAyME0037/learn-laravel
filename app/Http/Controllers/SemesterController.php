<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SemesterController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view.semesters')
            ->only('index', 'show');
        $this->middleware('permission:create.semesters')
            ->only('create', 'store');
        $this->middleware('permission:edit.semesters')
            ->only('edit', 'update');
        $this->middleware('permission:delete.semesters')
            ->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $semesters = Semester::with('academicYear')->paginate(10);
        return view('admin.semesters.index', compact('semesters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $academicYears = AcademicYear::all();
        return view('admin.semesters.create', compact('academicYears'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name'             => 'required|string|max:255',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after:start_date',
            'is_current'       => 'boolean',
        ]);

        Semester::create($validated);

        return redirect()->route('semesters.index')->with('success', 'Semester created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Semester $semester): View
    {
        $semester->load('academicYear');
        return view('admin.semesters.show', compact('semester'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Semester $semester): View
    {
        $academicYears = AcademicYear::all();
        return view('admin.semesters.edit', compact('semester', 'academicYears'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Semester $semester): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name'             => 'required|string|max:255',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after:start_date',
            'is_current'       => 'boolean',
        ]);

        $semester->update($validated);

        return redirect()->route('semesters.show', $semester)->with('success', 'Semester updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Semester $semester): RedirectResponse
    {
        $semester->delete();
        return redirect()->route('semesters.index')->with('success', 'Semester deleted successfully.');
    }
}
