<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instructor;

class InstructorController extends Controller
{
    public function index()
    {
        return view('admin.instructors.index');
    }

    public function create()
    {
        return view('admin.instructors.create');
    }

    public function show(Instructor $instructor)
    {
        $instructor->load(['user', 'department', 'address', 'contactDetail', 'classSessions']);
        return view('admin.instructors.show', compact('instructor'));
    }

    public function edit(Instructor $instructor)
    {
        return view('admin.instructors.edit', compact('instructor'));
    }
}