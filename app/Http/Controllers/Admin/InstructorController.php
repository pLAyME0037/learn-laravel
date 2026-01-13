<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instructor;

class InstructorController extends Controller
{
    public function show(Instructor $instructor)
    {
        $instructor->load(['user', 'department', 'address', 'contactDetail', 'classSessions']);
        return view('admin.instructors.show', compact('instructor'));
    }
}