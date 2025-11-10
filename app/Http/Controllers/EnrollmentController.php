<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view.enrollments')
            ->only('index', 'show');
        $this->middleware('permission:create.enrollments')
            ->only('create', 'store');
        $this->middleware('permission:edit.enrollments')
            ->only('edit', 'update');
        $this->middleware('permission:delete.enrollments')
            ->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $enrollments = Enrollment::with(['student.user', 'course', 'classSchedule'])
            ->paginate(10);
        return view('admin.enrollments.index', compact('enrollments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $students       = Student::all();
        $courses        = Course::all();
        $classSchedules = ClassSchedule::all();
        return view('admin.enrollments.create', compact('students', 'courses', 'classSchedules'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id'        => 'required|exists:students,id',
            'course_id'         => 'required|exists:courses,id',
            'enrollment_date'   => 'required|date',
            'status'            => 'required|string|max:255', // e.g., Enrolled, Completed, Dropped
            'class_schedule_id' => 'nullable|exists:class_schedules,id',
        ]);

        Enrollment::create($validated);

        return redirect()->route('admin.enrollments.index')->with('success', 'Enrollment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Enrollment $enrollment): View
    {
        $enrollment->load(['student', 'course', 'classSchedule']);
        return view('admin.enrollments.show', compact('enrollment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Enrollment $enrollment): View
    {
        $students       = Student::all();
        $courses        = Course::all();
        $classSchedules = ClassSchedule::all();
        return view('admin.enrollments.edit', compact('enrollment', 'students', 'courses', 'classSchedules'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Enrollment $enrollment): RedirectResponse
    {
        $validated = $request->validate([
            'student_id'        => 'required|exists:students,id',
            'course_id'         => 'required|exists:courses,id',
            'enrollment_date'   => 'required|date',
            'status'            => 'required|string|max:255',
            'class_schedule_id' => 'nullable|exists:class_schedules,id',
        ]);

        $enrollment->update($validated);

        return redirect()->route('admin.enrollments.show', $enrollment)->with('success', 'Enrollment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Enrollment $enrollment): RedirectResponse
    {
        $enrollment->delete();

        return redirect()->route('admin.enrollments.index')->with('success', 'Enrollment deleted successfully.');
    }
}
