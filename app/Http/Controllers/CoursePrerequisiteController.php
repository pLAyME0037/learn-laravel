<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CoursePrerequisite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CoursePrerequisiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $coursePrerequisites = CoursePrerequisite::with(['course', 'prerequisite'])->paginate(10);
        return view('admin.course_prerequisites.index', compact('coursePrerequisites'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $courses = Course::all();
        return view('admin.course_prerequisites.create', compact('courses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_id'              => 'required|exists:courses,id',
            'prerequisite_course_id' => 'required|exists:courses,id|different:course_id',
        ]);

        CoursePrerequisite::create($validated);

        return redirect()->route('admin.course_prerequisites.index')->with('success', 'Course prerequisite created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CoursePrerequisite $coursePrerequisite): View
    {
        $coursePrerequisite->load(['course', 'prerequisite']);
        return view('admin.course_prerequisites.show', compact('coursePrerequisite'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CoursePrerequisite $coursePrerequisite): View
    {
        $courses = Course::all();
        return view('admin.course_prerequisites.edit', compact('coursePrerequisite', 'courses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CoursePrerequisite $coursePrerequisite): RedirectResponse
    {
        $validated = $request->validate([
            'course_id'              => 'required|exists:courses,id',
            'prerequisite_course_id' => 'required|exists:courses,id|different:course_id',
        ]);

        $coursePrerequisite->update($validated);

        return redirect()->route('admin.course_prerequisites.show', $coursePrerequisite)
            ->with('success', 'Course prerequisite updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CoursePrerequisite $coursePrerequisite): RedirectResponse
    {
        $coursePrerequisite->delete();

        return redirect()->route('admin.course_prerequisites.index')
            ->with('success', 'Course prerequisite deleted successfully.');
    }
}
