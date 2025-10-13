<?php
namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $sort   = $request->get('sort', 'name');

        $departments = Department::with(
            'headOfDepartment',
            'programs',
            'faculty',
        )->withCount([
            'faculty',
            'students',
            'programs as active_programs_count' => function ($query) {
                $query->where('is_active', true);
            },
        ])->when($search, function ($query, $search) {
            return $query->search($search);
        })->when($status === 'active', function ($query) {
            return $query->active();
        })->when($status === 'inactive', function ($query) {
            return $query->where('is_active', false);
        })->when($sort, function ($query, $sort) {
            return match ($sort) {
                'name'          => $query->orderBy('name'),
                'code'          => $query->orderBy('code'),
                'faculty_count' => $query->orderBy('total_faculty', 'desc'),
                'student_count' => $query->orderBy('total_students', 'desc'),
                'budget'        => $query->orderBy('budget', 'desc'),
                default         => $query->ordered(),
            };
        })->paginate(15)->withQueryString()
        ;
        return view(
            'departments.index',
            compact(
                'departments',
                'search',
                'status',
                'sort'
            )
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $faculty = User::whereHas('role', function ($query) {
            $query->whereIn('slug', ['professor', 'HOD']);
        })->active()->get();

        return view('departments.create', compact('faculty'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                   => 'required
                |string
                |max:255
                |unique:departments,name',
            'code'                   => 'required
                |string
                |max:10
                |unique:departments,code',
            'description'            => 'nullable|string|max:1000',
            'email'                  => 'nullable|email|max:255',
            'phone'                  => 'nullable|string|max:20',
            'office_location'        => 'nullable|string|max:255',
            'head_of_deapartment_id' => 'nullable|exists:users,id',
            'founded_year'           => 'nullable|integer|min:1900|max:' . date('Y'),
            'budget'                 => 'required|numeric|min:0',
            'website'                => 'nullable|url|max:255',
            'display_order'          => 'nullable|integer|min:0',
            'is_active'              => 'boolean',
        ]);

        DB::transaction(function () use ($validated) {
            $department = Department::create($validated);
            // If HOD is assigned, update their department
            if ($validated['head_of_department_id'] ?? false) {
                User::where('id', $validated['head_of_department_id'])
                    ->update(['department_id' => $department->id]);
            }
        });
        return redirect()->route('admin.departments.index')
            ->with('success', 'Department created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        $department->load([
            'headOfDepartment',
            'programs' => function ($query) {
                $query->withCount('students')->active()->latest();
            },
            'faculty'  => function ($query) {
                $query->with('role')->active()->latest();
            },
        ]);

        $stats = [
            'total_programs'  => $department->programs->count(),
            'active_programs' => $department->programs->where('is_active', true)->count(),
            'total_faculty'   => $department->faculty->count(),
            'total_students'  => $department->students()->count(),
        ];

        return view(
            'departments.show',
            compact('department', 'stats')
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        $faculty = User::whereHas('role', function ($query) {
            $query->whereIn('slug', ['professor', 'HOD']);
        })->active()->get();
        return view(
            'departments.edit',
            compact('department', 'faculty')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required
                |string
                |max:255
                |unique:departments,name,' . $department->id,
            'code' => 'required
                |string
                |max:10
                |unique:departments,code,' . $department->id,
            'description' => 'required|string|max:1000',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'office_location' => 'nullable|string|max:255',
            'head_of_department_id' => 'nullable|exists:users,id',
            'founded_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'budget' => 'nullable|numeric|min:0',
            'website' => 'nullable|url|max:255',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        DB::transaction(function () use ($department, $validated) {
            $oldHodId = $department->head_of_department_id;
            $newHodId = $validated['head_of_department_id'] ?? null;

            $department->update($validated);

            // Update HOD department assignment
            if ($oldHodId !== $newHodId) {
                // Remove old HOD's department assignment
                if ($oldHodId) {
                    User::where('id', $oldHodId)
                    ->update(['department_id' => null]);
                }
                
                // Assign new HOD to department
                if ($newHodId) {
                    User::where('id', $newHodId)
                    ->update(['department_id' => $department->id]);
                }
            }
        });

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        if (!$department->canBeDeleted()) {
            return redirect()->route('admin.departments.index')
                ->with('error', 'Cannot delete department. It has associated faculty, students, or programs.');    
        }
        $department->delete();
        return redirect()->route('admin.departments.index')
            ->with('success', 'Department deleted successfully.');
    }

    /**
     * Restore sepcified soft deleted department
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        $department = Department::withTrashed()->findOrFail($id);
        $department->restore();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department restored successfully.');
    }

    /**
     * Update department Status
     * @param \App\Models\Department $department
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Department $department) {
        $department->update([
            'is_active' => !$department->is_active
        ]);

        $status = $department->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.departments.index')
            ->with('success', "Department {$status} successfully.");
    }

    public function getStatistics(){
        return [
            'total_departments' => Department::count(),
            'active_departments' => Department::active()->count(),
            'total_faculty' => User::whereHas('role', function($query) {
                $query->whereIn('slug', ['professor', 'HOD']);
            })->count(),
            'departments_with_hod' => Department::whereNotNull('head_of_department_id')->count(),
        ];
    }
}
