<?php
namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view.departments')
            ->only('index', 'show');
        $this->middleware('permission:create.departments')
            ->only('create', 'store');
        $this->middleware('permission:edit.departments')
            ->only('edit', 'update');
        $this->middleware('permission:delete.departments')
            ->only('destroy');
        $this->middleware('has_permission:delete.departments')
            ->only('restore', 'forceDelete');
    }

    public function index(Request $request): View
    {
        $search = $request->get('search');
        $status = $request->get('status', 'active');

        $departments = Department::with(['hod', 'users', 'programs'])
            ->withCount(['users', 'programs', 'students'])
            ->when($search, function ($query, $search) {
                return $query->search($search);
            })
            ->when($status === 'active', function ($query) {
                return $query->active();
            })
            ->when($status === 'inactive', function ($query) {
                return $query->where('is_active', false);
            })
            ->when($status === 'trashed', function ($query) {
                return $query->onlyTrashed();
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.departments.index', compact('departments', 'search', 'status'));
    }

    public function create(): View
    {
        $hods = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['hod', 'professor']);
        })->active()->get();
        return view('admin.departments.create', compact('hods'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'code'             => 'required|string|max:10|unique:departments',
            'description'      => 'nullable|string',
            'hod_id'           => 'nullable|exists:users,id',
            'email'            => 'nullable|email',
            'phone'            => 'nullable|string|max:20',
            'office_location'  => 'nullable|string|max:255',
            'established_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'budget'           => 'nullable|numeric|min:0',
            'is_active'        => 'boolean',
        ]);

        Department::create($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function show(Department $department): View
    {
        $department->load(['hod', 'users' => function ($query) {
            $query->with('roles')->latest()->take(10);
        }, 'programs']);

        return view('admin.departments.show', compact('department'));
    }

    public function edit(Department $department): View
    {
        $hods = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['hod', 'professor']);
        })->active()->get();
        return view('admin.departments.edit', compact('department', 'hods'));
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'code'             => 'required|string|max:10|unique:departments,code,' . $department->id,
            'description'      => 'nullable|string',
            'hod_id'           => 'nullable|exists:users,id',
            'email'            => 'nullable|email',
            'phone'            => 'nullable|string|max:20',
            'office_location'  => 'nullable|string|max:255',
            'established_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'budget'           => 'nullable|numeric|min:0',
            'is_active'        => 'boolean',
        ]);

        // Explicitly cast is_active to boolean to ensure correct storage
        $validated['is_active'] = (bool) $validated['is_active'];

        $department->update($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        if (! $department->canDelete()) {
            return redirect()->route('admin.departments.index')
                ->with('error', 'Cannot delete department with associated users or programs.');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department deleted successfully.');
    }

    public function restore($id): RedirectResponse
    {
        $department = Department::withTrashed()->findOrFail($id);
        $department->restore();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department restored successfully.');
    }

    public function forceDelete($id): RedirectResponse
    {
        $department = Department::withTrashed()->findOrFail($id);

        if (! $department->canDelete()) {
            return redirect()->route('admin.departments.index')
                ->with('error', 'Cannot permanently delete department with associated users or programs.');
        }

        $department->forceDelete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department permanently deleted.');
    }
}
