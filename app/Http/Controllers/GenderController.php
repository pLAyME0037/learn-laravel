<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Gender;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GenderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $genders = Gender::paginate(10);
        return view('admin.genders.index', compact('genders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.genders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:genders,name',
        ]);

        Gender::create($validated);

        return redirect()->route('admin.genders.index')->with('success', 'Gender created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Gender $gender): View
    {
        return view('admin.genders.show', compact('gender'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Gender $gender): View
    {
        return view('admin.genders.edit', compact('gender'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Gender $gender): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:genders,name,' . $gender->id,
        ]);

        $gender->update($validated);

        return redirect()->route('admin.genders.show', $gender)->with('success', 'Gender updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gender $gender): RedirectResponse
    {
        $gender->delete();

        return redirect()->route('admin.genders.index')->with('success', 'Gender deleted successfully.');
    }
}
