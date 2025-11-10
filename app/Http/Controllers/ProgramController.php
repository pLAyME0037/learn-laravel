<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Models\Degree;
use App\Models\Department;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view.programs')
            ->only('index', 'show');
        $this->middleware('permission:create.programs')
            ->only('create', 'store');
        $this->middleware('permission:edit.programs')
            ->only('edit', 'update');
        $this->middleware('permission:delete.programs')
            ->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $programs = Program::with('department', 'degree')->paginate(10);
        return view('admin.programs.index', compact('programs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $departments = Department::all();
        $degrees     = Degree::all();
        return view('admin.programs.create', compact('departments', 'degrees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255|unique:programs,name',
            'code'           => 'required|string|max:10|unique:programs,code',
            'department_id'  => 'required|exists:departments,id',
            'degree_id'      => 'required|exists:degrees,id',
            'description'    => 'nullable|string',
            'duration_years' => 'required|integer|min:1',
        ]);

        Program::create($validated);

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Program $program): View
    {
        $program->load(['department', 'degree']);
        return view('admin.programs.show', compact('program'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Program $program): View
    {
        $departments = Department::all();
        $degrees     = Degree::all();
        return view('admin.programs.edit', compact('program', 'departments', 'degrees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Program $program): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255|unique:programs,name,' . $program->id,
            'code'           => 'required|string|max:10|unique:programs,code,' . $program->id,
            'department_id'  => 'required|exists:departments,id',
            'degree_id'      => 'required|exists:degrees,id',
            'description'    => 'nullable|string',
            'duration_years' => 'required|integer|min:1',
        ]);

        $program->update($validated);

        return redirect()->route('admin.programs.show', $program)
            ->with('success', 'Program updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program $program): RedirectResponse
    {
        $program->delete();

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program deleted successfully.');
    }
}
