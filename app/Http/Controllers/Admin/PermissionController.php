<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $search      = $request->get('search');
        $permissions = Permission::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('group_name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            })
            ->with('roles')
            ->orderBy('name', 'asc') // Sort permissions alphabetically
            ->paginate(10);

        return view('admin.permissions.index', compact('permissions', 'search'));
    }
}
