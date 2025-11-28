<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstructorController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view.instructors')
            ->only('index', 'show');
        $this->middleware('permission:create.instructors')
            ->only('create', 'store');
        $this->middleware('permission:edit.instructors')
            ->only('edit', 'update');
        $this->middleware('permission:delete.instructors')
            ->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $instructors = Instructor::with(['user', 'department', 'courses.program'])->paginate(10);
        
        return view('admin.instructors.index', compact('instructors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $departments = Department::select('id', 'name')->orderBy('name')->get();
        $faculties   = Faculty::select('id', 'name')->orderBy('name')->get();

        $users = User::select('id', 'name')

            ->whereDoesntHave('roles', function ($query) {
                $query->whereIn('name', ['Super Administrator', 'admin', 'staff', 'student']);
            })
            ->whereHas('roles')
            ->get();
        return view('admin.instructors.create', compact('departments', 'faculties', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id'       => 'required|exists:users,id|unique:instructors,user_id',
            'faculty_id'    => 'required|exists:faculties,id',
            'department_id' => 'required|exists:departments,id',
            'payscale'      => 'required|integer',
        ]);

        Instructor::create($validated);

        return redirect()->route('admin.instructors.index')
            ->with('success', 'Instructor created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Instructor $instructor): View
    {
        $instructor->load('department');
        return view('admin.instructors.show', compact('instructor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Instructor $instructor): View
    {
        // Optimization: Only fetch ID and Name
        $departments = Department::select('id', 'name')->orderBy('name')->get();

        $users = User::select('id', 'name')
        // 1. Grouping: (User is NOT an instructor) OR (User is THIS instructor)
            ->where(function ($query) use ($instructor) {
                $query->whereDoesntHave('instructor')
                    ->orWhere('id', $instructor->user_id);
            })
        // 2. Logic: Exclude 'admin', 'staff', 'student'
            ->whereDoesntHave('roles', function ($query) {
                $query->whereIn('name', ['Super Administrator', 'admin', 'staff', 'student']);
            })
        // 3. Fix: Exclude users who have ZERO roles (The "no_role" concept)
            ->whereHas('roles')
            ->get();

        $faculties = Faculty::select('id', 'name')->orderBy('name')->get();

        return view(
            'admin.instructors.edit',
            compact(
                'instructor',
                'departments',
                'faculties', // Pass faculties to the view
                'users'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Instructor $instructor): RedirectResponse
    {
        $validated = $request->validate([
            'user_id'       => 'required|exists:users,id|unique:instructors,user_id,' . $instructor->id,
            'faculty_id'    => 'required|exists:faculties,id',
            'department_id' => 'required|exists:departments,id',
            'payscale'      => 'required|integer',
        ]);

        $instructor->update($validated);

        return redirect()->route('admin.instructors.show', $instructor)
            ->with('success', 'Instructor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Instructor $instructor): RedirectResponse
    {
        $instructor->delete();

        return redirect()->route('admin.instructors.index')
            ->with('success', 'Instructor deleted successfully.');
    }
}
