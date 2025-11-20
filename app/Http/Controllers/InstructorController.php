<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Models\Department;
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
        $instructors = Instructor::with(['user', 'department'])->paginate(10);
        return view('admin.instructors.index', compact('instructors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $departments = Department::all();
        $users       = User::all()->filter(
            fn($user) => $user->roles->isNotEmpty()
            && ! $user->hasAnyRole(['admin', 'staff', 'student'])
        );
        return view('admin.instructors.create', compact('departments', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id'       => 'required|exists:users,id|unique:instructors,user_id',
            'department_id' => 'required|exists:departments,id',
            'employee_id'   => 'required|string|max:255|unique:instructors,employee_id',
            'rank'          => 'nullable|string|max:255', // e.g., Professor, Associate Professor, Lecturer
            'hire_date'     => 'required|date',
        ]);

        Instructor::create($validated);

        return redirect()->route('admin.instructors.index')->with('success', 'Instructor created successfully.');
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
        $departments = Department::all();
        $users       = User::all()->filter(
            fn($user) => $user->roles->isNotEmpty()
            && ! $user->hasAnyRole(['admin', 'staff', 'student'])
        );

        return view(
            'admin.instructors.edit',
            compact(
                'instructor',
                'departments',
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
            'department_id' => 'required|exists:departments,id',
            'employee_id'   => 'required|string|max:255|unique:instructors,employee_id,' . $instructor->id,
            'rank'          => 'nullable|string|max:255',
            'hire_date'     => 'required|date',
        ]);

        $instructor->update($validated);

        return redirect()->route('admin.instructors.show', $instructor)->with('success', 'Instructor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Instructor $instructor): RedirectResponse
    {
        $instructor->delete();

        return redirect()->route('admin.instructors.index')->with('success', 'Instructor deleted successfully.');
    }
}
