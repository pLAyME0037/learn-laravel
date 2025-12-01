<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Import the Log facade
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view.academic-years')
            ->only('index', 'show');
        $this->middleware('permission:create.academic-years')
            ->only('create', 'store');
        $this->middleware('permission:edit.academic-years')
            ->only('edit', 'update');
        $this->middleware('permission:delete.academic-years')
            ->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $academicYears = AcademicYear::orderBy('is_current', 'desc')
        ->orderBy('start_date', 'desc')
        ->paginate(4);
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

        return view('admin.academic_years.show', compact('academicYear'));
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
        // 1. handle checkbox input
        $data = $request->all();
        $data['is_current'] = $request->boolean('is_current');

        // 2. enforce single current year
        if ($data['is_current']) {
            AcademicYear::query()->update(['is_current' => false]);
        }

        AcademicYear::create($data);

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
        $request->merge(['is_current' => $request->boolean('is_current')]);
        
        // 1. handle checkbox input
        $data = $request->all();
        $data['is_current'] = $request->boolean('is_current');

        // 2. enforce single current year
        if ($data['is_current']) {
            AcademicYear::query()->update(['is_current' => false]);
        }

        $academicYear->update($data);

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Academic Year updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        try {
            $academicYear->delete(); // Model event will handle cascading soft deletes
            return redirect()->route('admin.academic-years.index')
                ->with('success', 'Academic Year deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting academic year: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete academic year: ' . $e->getMessage());
        }
    }
}
