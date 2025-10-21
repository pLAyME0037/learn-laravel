<?php

namespace App\Http\Controllers;

use App\Models\SystemConfig;
use Illuminate\Http\Request;

class SystemConfigController extends Controller
{
    public function index()
    {
        $configs = SystemConfig::all()->keyBy('key');
        return view('system_configs.index', compact('configs'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'app_name' => 'nullable|string|max:255',
            'app_email' => 'nullable|email|max:255',
            'items_per_page' => 'nullable|integer|min:1',
            // Add other configuration keys as needed
        ]);

        foreach ($data as $key => $value) {
            SystemConfig::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->back()->with('success', 'System configurations updated successfully.');
    }
}
