<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Commune;
use App\Models\Department;
use App\Models\District;
use App\Models\Gender;
use App\Models\Program;
use App\Models\Province;
use App\Models\Student;
use App\Models\User;
use App\Models\Village;
use App\Services\StudentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $departments = Department::active()->select('id', 'name')->orderBy('name')->get();
        $programs    = Program::join('majors', 'programs.major_id', '=', 'majors.id')
            ->active()
            ->select('programs.id', 'programs.name', 'majors.department_id')
            ->orderBy('programs.name')
            ->get();
        $genders     = Gender::all();
        $provinces = Province::select('id', 'name_kh')->get();
        // Districts, Communes, and Villages will be loaded dynamically by Livewire

        return view(
            'admin.students.create',
            compact(
                'departments',
                'programs',
                'genders',
                'provinces',
            )
        );
    }

    public function store(StoreStudentRequest $request, StudentService $service): RedirectResponse
    {
        try {
            $service->registerStudent(
                $request->validated(),
                $request->file('profile_pic')
            );

            return redirect()->route('admin.students.index')
                ->with('success', 'Student created successfully.');

        } catch (\Exception $e) {
            Log::error('Student creation error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'System error: ' . $e->getMessage())
            ->withInput();
        }
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
        $student->load('user');
        $departments = Department::active()->select('id', 'name')->get();
        $programs    = Program::active()->select('id', 'name')->get();
        $genders     = Gender::all();

        return view('admin.students.edit',
            compact(
                'student',
                'departments',
                'programs',
                'genders',
            )
        );
    }
    public function update(UpdateStudentRequest $request, Student $student, StudentService $service): RedirectResponse
    {
        try {
            // Validate first (via UpdateStudentRequest), then pass data
            $service->updateStudent(
                $student,
                $request->validated(),
                $request->file('profile_pic')
            );

            return redirect()->route('admin.students.show', $student)
                ->with('success', 'Student updated successfully.');

        } catch (\Exception $e) {
            Log::error('Student update error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'System error: ' . $e->getMessage())
                ->withInput();
        }
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
