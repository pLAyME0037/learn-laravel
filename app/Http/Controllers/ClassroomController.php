<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassroomController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view.classrooms')
            ->only('index', 'show');
        $this->middleware('permission:create.classrooms')
            ->only('create', 'store');
        $this->middleware('permission:edit.classrooms')
            ->only('edit', 'update');
        $this->middleware('permission:delete.classrooms')
            ->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $classrooms = Classroom::paginate(10);
        return view('admin.classrooms.index', compact('classrooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.classrooms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255|unique:classrooms,name',
            'capacity' => 'required|integer|min:1',
            'room_number' => 'required|string|max:255', // e.g., 101, A203
            'type'     => 'nullable|string|max:255', // e.g., Lecture Hall, Lab, Seminar Room
            'location' => 'nullable|string|max:255',
        ]);

        Classroom::create($validated);

        return redirect()->route('admin.classrooms.index')
        ->with('success', 'Classroom created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom): View
    {
        return view('admin.classrooms.show', compact('classroom'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Classroom $classroom): View
    {
        return view('admin.classrooms.edit', compact('classroom'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classroom $classroom): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255|unique:classrooms,name,' . $classroom->id,
            'capacity' => 'required|integer|min:1',
            'room_number' => 'required|string|max:255', // e.g., 101, A203
            'type'     => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        $classroom->update($validated);

        return redirect()->route('admin.classrooms.show', $classroom)
        ->with('success', 'Classroom updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classroom $classroom): RedirectResponse
    {
        $classroom->delete();

        return redirect()->route('admin.classrooms.index')
        ->with('success', 'Classroom deleted successfully.');
    }
}
