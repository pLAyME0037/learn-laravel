<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassSchedule;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $attendances = Attendance::with(['student', 'course'])->paginate(10);
        return view('admin.attendances.index', compact('attendances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $students = Student::with('user')->get();
        $classSchedules = ClassSchedule::all();
        return view('admin.attendances.create', compact('students', 'classSchedules'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_schedule_id' => 'required|exists:class_schedules,id',
            'date' => 'required|date',
            'status' => 'required|string|max:255', // e.g., Present, Absent, Late
            'notes' => 'nullable|string',
        ]);

        Attendance::create($validated);

        return redirect()->route('admin.attendances.index')->with('success', 'Attendance record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance): View
    {
        return view('admin.attendances.show', compact('attendance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance): View
    {
        $students = \App\Models\Student::with('user')->get();
        $classSchedules = \App\Models\ClassSchedule::all();
        return view('admin.attendances.edit', compact('attendance', 'students', 'classSchedules'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_schedule_id' => 'required|exists:class_schedules,id',
            'date' => 'required|date',
            'status' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $attendance->update($validated);

        return redirect()->route('admin.attendances.show', $attendance)->with('success', 'Attendance record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance): RedirectResponse
    {
        $attendance->delete();

        return redirect()->route('admin.attendances.index')->with('success', 'Attendance record deleted successfully.');
    }
}
