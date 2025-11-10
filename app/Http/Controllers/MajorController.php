<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Models\Degree;
use App\Models\Department;
use App\Models\Major;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MajorController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view.majors')
            ->only('index', 'show');
        $this->middleware('permission:create.majors')
            ->only('create', 'store');
        $this->middleware('permission:edit.majors')
            ->only('edit', 'update');
        $this->middleware('permission:delete.majors')
            ->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $majors  = Major::with(['department', 'degree'])->paginate(10);
        $headers = [
            'ID',
            'Name',
            'Code',
            'Department',
            'Degree',
            'Created At',
            'Updated At',
        ];
        return view('admin.majors.index', compact('majors', 'headers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $departments = Department::all();
        $degrees     = Degree::all();
        return view('admin.majors.create', compact('departments', 'degrees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255|unique:majors,name',
            'code'          => 'required|string|max:10|unique:majors,code',
            'department_id' => 'required|exists:departments,id',
            'degree_id'     => 'required|exists:degrees,id',
            'description'   => 'nullable|string',
        ]);

        Major::create($validated);

        return redirect()->route('majors.index')->with('success', 'Major created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Major $major): View
    {
        $major->load(['department', 'degree']);
        return view('admin.majors.show', compact('major'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Major $major): View
    {
        $departments = Department::all();
        $degrees     = Degree::all();
        return view('admin.majors.edit', compact('major', 'departments', 'degrees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Major $major): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255|unique:majors,name,' . $major->id,
            'code'          => 'required|string|max:10|unique:majors,code,' . $major->id,
            'department_id' => 'required|exists:departments,id',
            'degree_id'     => 'required|exists:degrees,id',
            'description'   => 'nullable|string',
        ]);

        $major->update($validated);

        return redirect()->route('majors.show', $major)->with('success', 'Major updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Major $major): RedirectResponse
    {
        $major->delete();

        return redirect()->route('majors.index')->with('success', 'Major deleted successfully.');
    }
}
