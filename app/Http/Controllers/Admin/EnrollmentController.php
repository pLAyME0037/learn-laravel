<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view.enrollments')
            ->only('index', 'show');
        $this->middleware('permission:create.enrollments')
            ->only('create', 'store');
        $this->middleware('permission:edit.enrollments')
            ->only('edit', 'update');
        $this->middleware('permission:delete.enrollments')
            ->only('destroy');
    }

    /**
     * List all enrollments (Searchable).
     */
    public function index()
    {
        return view('admin.academic.enrollments.index');
    }

    /**
     * Manually enroll a student (Admin override).
     * Usually handled by Batch Enrollment tool, but useful for one-offs.
     */
    public function create()
    {
        return view('admin.academic.enrollments.create');
    }

    /**
     * Drop a student from a class.
     */
    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();
        // Observer will handle invoice deduction
        return back()->with('success', 'Student dropped from class.');
    }
}
