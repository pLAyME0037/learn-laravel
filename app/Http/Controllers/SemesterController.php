<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Import the Log facade
use Illuminate\Validation\Rule;
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
        $academicYears       = AcademicYear::all();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();
        return view('admin.semesters.create', compact('academicYears', 'currentAcademicYear'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Force the boolean value before validation
        $request->merge(['is_active' => $request->boolean('is_active')]);

        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name'             => 'required|string|max:255',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after:start_date',
            'is_active'        => 'boolean',
        ]);

        Semester::create($validated);

        return redirect()->route('admin.semesters.index')
            ->with('success', 'Semester created successfully.');
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
        $request->merge(['is_active' => $request->boolean('is_active')]);
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name'             => [
                'required',
                'string',
                Rule::unique('semester')->where(fn($q) =>
                    $q->where('academic_year_id', $request->academic_year_id))
                    ->ignore($semester->id),
            ],
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after:start_date',
            'is_active'        => 'boolean',
        ]);

        $semester->update($validated);

        return redirect()->route('admin.semesters.show', $semester)
            ->with('success', 'Semester updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Semester $semester): RedirectResponse
    {
        try {
            DB::transaction(function () use ($semester) {
                $semester->classSchedules()->delete(); // Mass delete (faster)
                $semester->courses()->delete();        // Mass delete (faster)
                $semester->delete();
            });
            DB::commit();
            return redirect()->route('admin.semesters.index')
                ->with('success', 'Semester deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting semester: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete semester: ' . $e->getMessage());
        }
    }
}
