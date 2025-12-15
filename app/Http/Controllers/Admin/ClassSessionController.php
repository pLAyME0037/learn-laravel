<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use Illuminate\Http\Request;

class ClassSessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view.class-schedules')->only('index', 'show');
        $this->middleware('permission:create.class-schedules')->only('create', 'store');
        $this->middleware('permission:edit.class-schedules')->only('edit', 'update');
        $this->middleware('permission:delete.class-schedules')->only('destroy');
    }

    /**
     * Display schedule for a specific semester.
     * Filterable by Course or Instructor.
     */
    public function index(Request $request)
    {
        // View handled by Livewire Component usually,
        // but if using Controller:
        return view('admin.academic.schedule.index');
    }

    /**
     * Show create form (Add a class to the schedule).
     */
    public function create()
    {
        // Need to load Active Semester, Courses, Instructors
        return view('admin.academic.schedule.create');
    }

    public function show(ClassSession $classSession)
    {
        $classSession->load([
            'course', 
            'instructor', 
            'enrollments.student.user'
        ]);
        return view('admin.academic.schedule.show', compact('classSession'));
    }

    public function edit(ClassSession $classSession)
    {
        return view('admin.academic.schedule.edit', compact('classSession'));
    }
}
