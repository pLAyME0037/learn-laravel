<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Models\Faculty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacultyController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view.faculties')
            ->only('index', 'show');
        $this->middleware('permission:create.faculties')
            ->only('create', 'store');
        $this->middleware('permission:edit.faculties')
            ->only('edit', 'update');
        $this->middleware('permission:delete.faculties')
            ->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $faculties = Faculty::paginate(10);
        return view('admin.faculties.index', compact('faculties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $users = \App\Models\User::all();
        return view('admin.faculties.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:faculties,name',
            'dean_id'     => 'nullable|exists:users,id',
            'description' => 'nullable|string',
        ]);

        Faculty::create($validated);

        return redirect()->route('admin.faculties.index')->with('success', 'Faculty created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Faculty $faculty): View
    {
        return view('admin.faculties.show', compact('faculty'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Faculty $faculty): View
    {
        $users = \App\Models\User::all();
        return view('admin.faculties.edit', compact('faculty', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Faculty $faculty): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:faculties,name,' . $faculty->id,
            'dean_id'     => 'nullable|exists:users,id',
            'description' => 'nullable|string',
        ]);

        $faculty->update($validated);

        return redirect()->route('admin.faculties.show', $faculty)->with('success', 'Faculty updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faculty $faculty): RedirectResponse
    {
        $faculty->delete();

        return redirect()->route('admin.faculties.index')->with('success', 'Faculty deleted successfully.');
    }
}
