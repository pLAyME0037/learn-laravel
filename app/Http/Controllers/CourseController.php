<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Department;
use App\Models\Semester;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view.courses')
            ->only('index', 'show');
        $this->middleware('permission:create.courses')
            ->only('create', 'store');
        $this->middleware('permission:edit.courses')
            ->only('edit', 'update');
        $this->middleware('permission:delete.courses')
            ->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $courses = Course::with(['department', 'instructors', 'semester'])
            ->paginate(10);
        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $departments = Department::all();
        $semesters   = Semester::all();
        return view('admin.courses.create', compact('departments', 'semesters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'code'          => 'required|string|max:255|unique:courses,code',
            'credits'       => 'required|integer|min:1',
            'description'   => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'semester_id'   => 'required|exists:semesters,id',
        ]);

        Course::create($validated);

        return redirect()->route('admin.courses.index')->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course): View
    {
        $course->load(['department', 'semester', 'prerequisites', 'prerequisiteForCourses']);
        return view('admin.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course): View
    {
        $departments = Department::all();
        $semesters   = Semester::all();
        return view('admin.courses.edit', compact('course', 'departments', 'semesters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'code'          => 'required|string|max:255|unique:courses,code,' . $course->id,
            'credits'       => 'required|integer|min:1',
            'description'   => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'semester_id'   => 'required|exists:semesters,id',
        ]);

        $course->update($validated);

        return redirect()->route('admin.courses.show', $course)
        ->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();

        return redirect()->route('admin.courses.index')
        ->with('success', 'Course deleted successfully.');
    }
}
