<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\SystemConfig;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SystemConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $systemConfigs = SystemConfig::paginate(10);
        return view('admin.system_configs.index', compact('systemConfigs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.system_configs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:system_configs,key',
            'value' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        SystemConfig::create($validated);

        return redirect()->route('system-configs.index')->with('success', 'System configuration created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SystemConfig $systemConfig): View
    {
        return view('admin.system_configs.show', compact('systemConfig'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SystemConfig $systemConfig): View
    {
        return view('admin.system_configs.edit', compact('systemConfig'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SystemConfig $systemConfig): RedirectResponse
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:system_configs,key,' . $systemConfig->id,
            'value' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $systemConfig->update($validated);

        return redirect()->route('system-configs.show', $systemConfig)->with('success', 'System configuration updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SystemConfig $systemConfig): RedirectResponse
    {
        $systemConfig->delete();

        return redirect()->route('system-configs.index')->with('success', 'System configuration deleted successfully.');
    }
}
