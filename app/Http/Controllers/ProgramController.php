<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Models\Degree;
use App\Models\Department;
use App\Models\Major;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view.programs')->only('index', 'show');
        $this->middleware('permission:create.programs')->only('create', 'store');
        $this->middleware('permission:edit.programs')->only('edit', 'update');
        $this->middleware('permission:delete.programs')->only('destroy');
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
        $departments = Department::active()->select('id', 'name')->get();
        $degrees     = Degree::select('id', 'name')->get();
        $majors      = Major::select('id', 'name')->get();
        return view('admin.programs.create', compact('departments', 'degrees', 'majors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255|unique:programs,name,',
            'major_id'  => 'required|exists:majors,id', // Corrected validation
            'degree_id' => 'required|exists:degrees,id',
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
        $majors      = Major::all(); // Fetch all majors for edit view
        return view('admin.programs.edit', compact('program', 'departments', 'degrees', 'majors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Program $program): RedirectResponse
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255|unique:programs,name,' . $program->id,
            'major_id'  => 'required|exists:majors,id', // Corrected validation
            'degree_id' => 'required|exists:degrees,id',
        ]);

        $program->update($validated);

        return redirect()->route('admin.programs.index')
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
