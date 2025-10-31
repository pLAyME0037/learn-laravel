<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\Classroom;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClassScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $classSchedules = ClassSchedule::with(['course', 'professor', 'classroom', 'semester'])->paginate(10);
        return view('admin.class_schedules.index', compact('classSchedules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $courses = Course::all();
        $instructors = Instructor::all();
        $classrooms = Classroom::all();
        $semesters = Semester::all();
        return view('admin.class_schedules.create', compact('courses', 'instructors', 'classrooms', 'semesters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'instructor_id' => 'required|exists:instructors,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'semester_id' => 'required|exists:semesters,id',
            'day_of_week' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        ClassSchedule::create($validated);

        return redirect()->route('admin.class_schedules.index')->with('success', 'Class schedule created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassSchedule $classSchedule): View
    {
        $classSchedule->load(['course', 'professor', 'classroom', 'semester']);
        return view('admin.class_schedules.show', compact('classSchedule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClassSchedule $classSchedule): View
    {
        $courses = Course::all();
        $instructors = Instructor::all();
        $classrooms = Classroom::all();
        $semesters = Semester::all();
        return view('admin.class_schedules.edit', compact('classSchedule', 'courses', 'instructors', 'classrooms', 'semesters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClassSchedule $classSchedule): RedirectResponse
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'instructor_id' => 'required|exists:instructors,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'semester_id' => 'required|exists:semesters,id',
            'day_of_week' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $classSchedule->update($validated);

        return redirect()->route('admin.class_schedules.show', $classSchedule)->with('success', 'Class schedule updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassSchedule $classSchedule): RedirectResponse
    {
        $classSchedule->delete();

        return redirect()->route('admin.class_schedules.index')->with('success', 'Class schedule deleted successfully.');
    }
}
