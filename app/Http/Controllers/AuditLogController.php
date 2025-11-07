<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view.audit-logs')
            ->only('index', 'show');
        $this->middleware('permission:create.audit-logs')
            ->only('create', 'store');
        $this->middleware('permission:edit.audit-logs')
            ->only('edit', 'update');
        $this->middleware('permission:delete.audit-logs')
            ->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $auditLogs = AuditLog::with('user')->paginate(10);
        return view('admin.audit_logs.index', compact('auditLogs'));
    }

    /**
     * Display the specified resource.
     */
    public function show(AuditLog $auditLog): View
    {
        return view('admin.audit_logs.show', compact('auditLog'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AuditLog $auditLog): RedirectResponse
    {
        $auditLog->delete();

        return redirect()->route('admin.audit_logs.index')->with('success', 'Audit log deleted successfully.');
    }
}
