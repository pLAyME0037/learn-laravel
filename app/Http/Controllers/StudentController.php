<?php
namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view.students')->only('index', 'show');
        $this->middleware('permission:create.students')->only('create', 'store');
        $this->middleware('permission:edit.students')->only('edit', 'update');
        $this->middleware('permission:delete.students')->only('destroy');
        $this->middleware('has_permission:delete.students')->only('restore', 'forceDelete');
        $this->middleware('has_permission:edit.students')->only('updateStatus');
    }
    public function index(Request $request): View
    {
        $search     = $request->get('search');
        $department = $request->get('department');
        $program    = $request->get('program');
        $studentId  = $request->get('student_id');
        $status     = $request->get('status', 'active');

        $students = Student::with(['user', 'department', 'program'])
            ->when($search, function ($query, $search) {
                return $query->search($search);
            })
            ->when($department, function ($query, $department) {
                return $query->where('department_id', $department);
            })
            ->when($program, function ($query, $program) {
                return $query->where('program_id', $program);
            })
            ->when($studentId, function ($query, $studentId) {
                return $query->where('student_id', $studentId);
            })
            ->when($status === 'active', function ($query) {
                return $query->active();
            })
            ->when($status === 'inactive', function ($query) {
                return $query->where('academic_status', '!=', 'active');
            })
            ->when($status === 'trashed', function ($query) {
                return $query->onlyTrashed();
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $departments = Department::active()->get();
        $programs    = Program::active()->get();

        return view('admin.students.index', compact(
            'students',
            'search',
            'department',
            'program',
            'studentId',
            'status',
            'departments',
            'programs'
        ));
    }

    public function create(): View
    {
        $departments = Department::active()->get();
        $programs    = Program::active()->get();

        return view('admin.students.create', compact('departments', 'programs'));
    }

    public function store(Request $request): RedirectResponse
    {
        DB::beginTransaction();

        try {
            // Validate user data
            $userData = $request->validate([
                'name'        => 'required|string|max:255',
                'email'       => 'required|string|email|max:255|unique:users',
                'username'    => 'required|string|max:255|alpha_dash|unique:users',
                'password'    => 'required|string|min:8|confirmed',
                'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Validate student data
            $studentData = $request->validate([
                'department_id'              => 'required|exists:departments,id',
                'program_id'                 => 'required|exists:programs,id',
                'date_of_birth'              => 'required|date',
                'gender'                     => 'required|in:male,female,other',
                'nationality'                => 'required|string|max:100',
                'phone'                      => 'required|string|max:20',
                'emergency_contact_name'     => 'required|string|max:255',
                'emergency_contact_phone'    => 'required|string|max:20',
                'emergency_contact_relation' => 'required|string|max:100',
                'current_address'            => 'required|string',
                'permanent_address'          => 'required|string',
                'city'                       => 'required|string|max:100',
                'state'                      => 'required|string|max:100',
                'country'                    => 'required|string|max:100',
                'postal_code'                => 'required|string|max:20',
                'admission_date'             => 'required|date',
                'enrollment_status'          => 'required|in:full_time,part_time,exchange,study_abroad',
                'fee_category'               => 'required|in:regular,scholarship,financial_aid,self_financed',
                'previous_education'         => 'nullable|string',
                'blood_group'                => 'nullable|string|max:10',
                'has_disability'             => 'boolean',
                'disability_details'         => 'nullable|string',
            ]);

            // Create user
            $user = User::create([
                'name'          => $userData['name'],
                'email'         => $userData['email'],
                'username'      => $userData['username'],
                'password'      => Hash::make($userData['password']),
                'department_id' => $studentData['department_id'],
                'is_active'     => true,
            ]);

            // Assign student role
            $user->assignRole('student');

            // Handle profile picture upload
            if ($request->hasFile('profile_pic')) {
                $path = $request->file('profile_pic')->store('profile-pictures', 'public');
                $user->update(['profile_pic' => $path]);
            }

            // Create student record
            Student::create(array_merge($studentData, [
                'user_id'    => $user->id,
                'student_id' => (new Student())->generateStudentId(),
            ]));

            DB::commit();

            return redirect()->route('admin.students.index')
                ->with('success', 'Student created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log the exception for debugging purposes
            \Log::error('Student creation failed: ' . $e->getMessage(), [
                'exception'    => $e,
                'request_data' => $request->all(),
            ]);

            return redirect()->back()
                ->with(
                    'error',
                    'Failed to create student. Please check the form data and try again. If the problem persists, contact support.'
                )
                ->withInput();
        }
    }

    public function show(Student $student): View
    {
        $student->load(['user', 'department', 'program', 'enrollments', 'academicRecords']);

        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student): View
    {
        $student->load('user');
        $departments = Department::active()->get();
        $programs    = Program::active()->get();

        return view('admin.students.edit',
            compact(
                'student',
                'departments', 'programs'
            )
        );
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        DB::beginTransaction();

        try {
            // Validate user data
            $userData = $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required
                    |string
                    |email
                    |max:255
                    |unique:users,email,' . $student->user_id,
                'username' => 'required
                    |string
                    |max:255
                    |alpha_dash
                    |unique:users,username,' . $student->user_id,
            ]);

            // Validate student data
            $studentData = $request->validate([
                'department_id'              => 'required|exists:departments,id',
                'program_id'                 => 'required|exists:programs,id',
                'date_of_birth'              => 'required|date',
                'gender'                     => 'required|in:male,female,other',
                'nationality'                => 'required|string|max:100',
                'phone'                      => 'required|string|max:20',
                'emergency_contact_name'     => 'required|string|max:255',
                'emergency_contact_phone'    => 'required|string|max:20',
                'emergency_contact_relation' => 'required|string|max:100',
                'current_address'            => 'required|string',
                'permanent_address'          => 'required|string',
                'city'                       => 'required|string|max:100',
                'state'                      => 'required|string|max:100',
                'country'                    => 'required|string|max:100',
                'postal_code'                => 'required|string|max:20',
                'admission_date'             => 'required|date',
                'expected_graduation'        => 'nullable|date',
                'current_semester'           => 'required|integer|min:1|max:12',
                'academic_status'            => 'required|in:active,probation,suspended,graduated,withdrawn,transfered',
                'enrollment_status'          => 'required|in:full_time,part_time,exchange,study_abroad',
                'fee_category'               => 'required|in:regular,scholarship,financial_aid,self_financed',
                'has_outstanding_balance'    => 'boolean',
                'previous_education'         => 'nullable|string',
                'blood_group'                => 'nullable|string|max:10',
                'has_disability'             => 'boolean',
                'disability_details'         => 'nullable|string',
            ]);

            // Update user
            $student->user->update($userData);

            // Handle profile picture upload
            if ($request->hasFile('profile_pic')) {
                $path = $request->file('profile_pic')->store('profile-pictures', 'public');
                $student->user->update(['profile_pic' => $path]);
            }

            // Update student
            $student->update($studentData);

            DB::commit();

            return redirect()->route('admin.students.show', $student)
                ->with('success', 'Student updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update student: ' . $e->getMessage())
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
