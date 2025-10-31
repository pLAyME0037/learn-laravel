<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuditLogController extends Controller
{
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
