<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
     * List all courses.
     */
    public function index()
    {
        // View will likely use a Livewire table, but we pass basic data just in case
        return view('admin.courses.index');
    }

    /**
     * Show create form.
     */
    public function create()
    {
        // Departments needed for dropdown
        $departments = Department::orderBy('name')->pluck('name', 'id');
        // Other courses needed for prerequisites list
        $allCourses = Course::orderBy('code')->pluck('code', 'id');

        return view('admin.courses.create', compact('departments', 'allCourses'));
    }

    /**
     * Store a new course.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:courses,code',
            'credits' => 'required|integer|min:0|max:10',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'exists:courses,id',
        ]);

        $course = Course::create($validated);

        if (!empty($validated['prerequisites'])) {
            $course->prerequisites()->sync($validated['prerequisites']);
        }

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(Course $course)
    {
        $departments = Department::orderBy('name')->pluck('name', 'id');
        
        // Exclude self from prerequisite list to prevent self-dependency
        $allCourses = Course::where('id', '!=', $course->id)
            ->orderBy('code')
            ->get()
            ->mapWithKeys(fn($c) => [$c->id => "{$c->code} - {$c->name}"]);

        // Get currently selected prerequisite IDs
        $selectedPrereqs = $course->prerequisites()->pluck('courses.id')->toArray();

        return view('admin.courses.edit', compact(
            'course', 
            'departments', 
            'allCourses', 
            'selectedPrereqs'
        ));
    }

    /**
     * Update course.
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:20', Rule::unique('courses')->ignore($course->id)],
            'credits' => 'required|integer|min:0|max:10',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'exists:courses,id',
        ]);

        $course->update($validated);

        if (isset($validated['prerequisites'])) {
            $course->prerequisites()->sync($validated['prerequisites']);
        } else {
            $course->prerequisites()->detach();
        }

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        // Optional: Check if used in enrollments/program_structures before deleting?
        // SoftDeletes handles basic safety.
        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted.');
    }
}
