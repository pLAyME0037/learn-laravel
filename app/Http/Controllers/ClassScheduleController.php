<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\ClassSchedule;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\Semester;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view.class-schedules')->only('index', 'show');
        $this->middleware('permission:create.class-schedules')->only('create', 'store');
        $this->middleware('permission:edit.class-schedules')->only('edit', 'update');
        $this->middleware('permission:delete.class-schedules')->only('destroy');
    }

    public function index(): View
    {
        $classSchedules = ClassSchedule::with([
            'course:id,name,code',
            'instructor:id,user_id',
            'instructor.user:id,name',
            'classroom:id,room_number,building_name',
            'semester:id,name',
        ])
            ->select(
                'id',
                'course_id',
                'instructor_id',
                'classroom_id',
                'semester_id',
                'day_of_week',
                'start_time',
                'end_time'
            )->latest()->paginate(10);

        return view('admin.class_schedules.index', compact('classSchedules'));
    }

    public function create(): View
    {
        // Optimization: Only fetch ID and Label columns. Order for better UX.
        $courses = Course::select(
            'id',
            'name',
            'code'
        )->orderBy('name')->get();
        $instructors = Instructor::with('user:id,name')->get();
        $classrooms  = Classroom::select(
            'id',
            'room_number',
            'building_name',
            'capacity'
        )->orderBy('room_number')->get();
        $semesters = Semester::select(
            'id',
            'name'
        )->where('is_active', true)->get();

        return view('admin.class_schedules.create', compact(
            'courses',
            'instructors',
            'classrooms',
            'semesters'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = ClassSchedule::validateSchedule($request); // Use static method from model

        ClassSchedule::checkForConflicts(
            $validated['classroom_id'],
            $validated['instructor_id'],
            $validated['day_of_week'],
            $validated['start_time'],
            $validated['end_time'],
            $validated['semester_id']
        );

        ClassSchedule::create($validated);

        return redirect()->route('admin.class-schedules.index')
            ->with('success', 'Class schedule created successfully.');
    }

    public function show(ClassSchedule $classSchedule): View
    {
        $classSchedule->load([
            'course:id,name,code',
            'instructor.user:id,name', // Changed from professor.user to instructor.user
            'classroom:id,room_number',
            'semester:id,name',
        ]);

        return view('admin.class_schedules.show', compact('classSchedule'));
    }

    public function edit(ClassSchedule $classSchedule): View
    {
        $courses = Course::select(
            'id',
            'name',
            'code'
        )->orderBy('name')->get();
        $instructors = Instructor::whereHas('user.roles', function ($query) {
            $query->where('name', 'professor');
        })->with('user:id,name')->get()->unique('user_id');
        $classrooms = Classroom::select(
            'id',
            'room_number',
            'building_name'
        )->orderBy('room_number')->get();
        $semesters = Semester::select('id', 'name')->get();

        return view('admin.class_schedules.edit', compact(
            'classSchedule',
            'courses',
            'instructors',
            'classrooms',
            'semesters'
        ));
    }

    public function update(Request $request, ClassSchedule $classSchedule): RedirectResponse
    {
        $validated = ClassSchedule::validateSchedule($request);

        // Conflict Check (Update - Pass current ID to ignore self)
        ClassSchedule::checkForConflicts(
            $validated['classroom_id'],
            $validated['instructor_id'],
            $validated['day_of_week'],
            $validated['start_time'],
            $validated['end_time'],
            $validated['semester_id'],
            $classSchedule->id
        );

        $classSchedule->update($validated);

        return redirect()->route('admin.class-schedules.show', $classSchedule)
            ->with('success', 'Class schedule updated successfully.');
    }

    public function destroy(ClassSchedule $classSchedule): RedirectResponse
    {
        $classSchedule->delete();

        return redirect()->route('admin.class-schedules.index')
            ->with('success', 'Class schedule deleted successfully.');
    }
}
