<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Models\Degree;
use App\Models\Faculty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DegreeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view.degrees')
            ->only('index', 'show');
        $this->middleware('permission:create.degrees')
            ->only('create', 'store');
        $this->middleware('permission:edit.degrees')
            ->only('edit', 'update');
        $this->middleware('permission:delete.degrees')
            ->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $degrees = Degree::with('faculty')->paginate(10);
        return view('admin.degrees.index', compact('degrees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $faculties = Faculty::all();
        return view('admin.degrees.create', compact('faculties'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:degrees,name',
            'level'       => 'required|string|max:255', // e.g., Bachelor, Master, PhD
            'faculty_id'  => 'required|exists:faculties,id',
            'description' => 'nullable|string',
        ]);

        Degree::create($validated);

        return redirect()->route('admin.degrees.index')->with('success', 'Degree created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Degree $degree): View
    {
        $degree->load('faculty');
        return view('admin.degrees.show', compact('degree'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Degree $degree): View
    {
        $faculties = Faculty::all();
        return view('admin.degrees.edit', compact('degree', 'faculties'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Degree $degree): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:degrees,name,' . $degree->id,
            'level'       => 'required|string|max:255',
            'faculty_id'  => 'required|exists:faculties,id',
            'description' => 'nullable|string',
        ]);

        $degree->update($validated);

        return redirect()->route('admin.degrees.show', $degree)->with('success', 'Degree updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Degree $degree): RedirectResponse
    {
        $degree->delete();

        return redirect()->route('admin.degrees.index')->with('success', 'Degree deleted successfully.');
    }
}
