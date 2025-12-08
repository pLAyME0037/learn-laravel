<?php
namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Program;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view.students')->only('index', 'show');
        $this->middleware('permission:create.students')->only('create', 'store');
        $this->middleware('permission:edit.students')->only('edit', 'update');
        $this->middleware('permission:delete.students')->only('destroy', 'restore');
        $this->middleware('has_permission:delete.students')->only('forceDelete');
        $this->middleware('has_permission:edit.students')->only('updateStatus');
    }

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search'            => 'nullable|string|max:100',
            'department_id'     => 'nullable|integer|exists:departments,id',
            'program_id'        => 'nullable|integer|exists:programs,id',
            'academic_status'   => 'nullable|string|in:active,probation,suspended,graduated,withdrawn,transfered,trashed',
            'enrollment_status' => 'nullable|string|in:full_time,part_time,exchange,study_abroad',
        ]);

        $students = Student::with(['department', 'program'])
            ->applyFilters($filters)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $departments = Department::active()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        $programs = Program::join('majors', 'programs.major_id', '=', 'majors.id')
            ->active()
            ->select(
                'programs.id',
                'programs.name',
                'majors.department_id',
            )
            ->orderBy('programs.name')
            ->get();

        return view('admin.students.index', compact(
            'filters',
            'students',
            'departments',
            'programs',
        ));
    }

    public function create(): View
    {
        // Data loading is now handled by the Livewire Component's mount() method
        return view('admin.students.create');
    }

    public function show(Student $student): View
    {
        $student->load([
            'user',
            'department',
            'program',
            'gender',
            'enrollments.course',
            'enrollments.semester',
            'academicRecords',
        ]);

        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student): View
    {
        // Dropdown data loading is now handled by the Livewire Component's mount() method
        // Only pass the student so the blade view can pass the ID to Livewire:
        // <livewire:admin.students.edit-student :id="$student->id" />
        return view('admin.students.edit', compact('student'));
    }

    public function destroy(Student $student): RedirectResponse
    {
        DB::beginTransaction();

        try {
            // Soft delete student and user
            $student->delete();
            $student->user->delete();

            DB::commit();

            return redirect()->route('admin.students.index')
                ->with('success', 'Student deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.students.index')
                ->with('error', 'Failed to delete student: ' . $e->getMessage());
        }
    }

    public function restore($id): RedirectResponse
    {
        $student = Student::withTrashed()->findOrFail($id);

        DB::beginTransaction();

        try {
            $student->restore();
            $student->user()->restore();

            DB::commit();

            return redirect()->route('admin.students.index')
                ->with('success', 'Student restored successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.students.index')
                ->with('error', 'Failed to restore student: ' . $e->getMessage());
        }
    }

    public function forceDelete($id): RedirectResponse
    {
        $student = Student::withTrashed()->findOrFail($id);

        DB::beginTransaction();

        try {
            // Permanently delete student and user
            $student->forceDelete();
            $student->user()->forceDelete();

            DB::commit();

            return redirect()->route('admin.students.index')
                ->with('success', 'Student permanently deleted.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.students.index')
                ->with('error', 'Failed to permanently delete student: ' . $e->getMessage());
        }
    }

    public function updateStatus(Student $student): RedirectResponse
    {
        $request = request();

        $validated = $request->validate([
            'academic_status' => 'required|in:active,probation,suspended,graduated,withdrawn,transfered',
        ]);

        $student->update($validated);

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'Student status updated successfully.');
    }
}
