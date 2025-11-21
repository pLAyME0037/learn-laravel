<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassSchedule;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Semester;
use App\Models\Student;
use Carbon\Carbon;
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
        // Plan to use AJAX(Livewire in future)
        $students = Student::join('users', 'students.user_id', '=', 'users.id')
            ->select('students.id', 'users.name')
            ->orderBy('users.name')
            ->get();
        $semesters     = Semester::select('id', 'name')->orderBy('name')->get();
        $courses       = Course::select('id', 'name')->orderBy('name')->get();
        $academicYears = AcademicYear::select('id', 'name')
            ->orderByDesc('name')
            ->get();
        $classSchedules = ClassSchedule::join('courses', 'class_schedules.course_id', '=', 'courses.id')
            ->select('class_schedules.id', 'courses.name as course_name', 'class_schedules.day_of_week', 'class_schedules.start_time')
            ->orderBy('courses.name')
            ->get();

        return view('admin.enrollments.create', compact(
            'students',
            'semesters',
            'academicYears',
            'courses',
            'classSchedules'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id'        => 'required|exists:students,id',
            'academic_year_id'  => 'required|exists:academic_years,id',
            'course_id'         => 'required|exists:courses,id',
            'enrollment_date'   => 'required|date',
            'status'            => 'required|string|max:255', // e.g., Enrolled, Completed, Dropped
            'class_schedule_id' => 'nullable|exists:class_schedules,id',
        ]);

        Enrollment::create($validated);

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Enrollment created successfully.');
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
     * show form for editing
     */
    public function edit(Enrollment $enrollment): View
    {
        // Plan to use AJAX(Livewire in future)
        $students = Student::join('users', 'students.user_id', '=', 'users.id')
            ->select('students.id', 'users.name')
            ->orderBy('users.name')
            ->get();
        $semesters     = Semester::select('id', 'name')->orderBy('name')->get();
        $courses       = Course::select('id', 'name')->orderBy('name')->get();
        $academicYears = AcademicYear::select('id', 'name')
            ->orderByDesc('name')
            ->get();
        $classSchedules = ClassSchedule::join('courses', 'class_schedules.course_id', '=', 'courses.id')
            ->select(
                'class_schedules.id',
                'class_schedules.day_of_week',
                'class_schedules.start_time',
                'courses.name as course_name',
                'courses.code as code',
            )
            ->orderBy('class_schedules.day_of_week')
            ->orderBy('class_schedules.start_time')
            ->get()
            ->map(function ($schedule) {
                $time = Carbon::parse($schedule->start_time)
                    ->format('d-M-Y h:i A');
                $schedule->formatted_time = $time;
                // CRITICAL: Create the "Main Label" for the trigger box here
                // This keeps the JS component generic. It just looks for 'display_label'
                $schedule->display_label = "Code: {$schedule->code} | {$schedule->course_name} | {$schedule->day_of_week} | {$time}";
                return $schedule;
            });

        return view('admin.enrollments.edit', compact(
            'enrollment',
            'students',
            'semesters',
            'courses',
            'academicYears',
            'classSchedules'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Enrollment $enrollment): RedirectResponse
    {
        $validated = $request->validate([
            'student_id'        => 'required|exists:students,id',
            'academic_year_id'  => 'required|exists:academic_years,id',
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
