<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemConfig;
use Illuminate\Http\Request;

class SystemConfigController extends Controller
{
    /**
     * Display the Settings Form.
     */
    public function index()
    {
        // 1. Define all possible settings and their defaults (Structure)
        // This allows the View to render them even if they aren't in the DB yet.
        $settingsSchema = [
            'General' => [
                'university_name' => ['label' => 'University Name', 'type' => 'text'],
                'site_logo'       => ['label' => 'Logo URL', 'type' => 'text'],
                'contact_email'   => ['label' => 'Contact Email', 'type' => 'email'],
            ],
            'Academic' => [
                'current_academic_year' => ['label' => 'Current Academic Year', 'type' => 'text'],
                'allow_registration'    => ['label' => 'Allow Student Registration', 'type' => 'boolean'],
            ],
            'Finance' => [
                'currency_symbol' => ['label' => 'Currency Symbol', 'type' => 'text'],
                'cost_per_credit' => ['label' => 'Default Cost Per Credit', 'type' => 'number'],
            ]
        ];

        // 2. Fetch current values from DB
        $currentValues = SystemConfig::all()->pluck('value', 'key');

        return view('admin.settings.index', compact('settingsSchema', 'currentValues'));
    }

    /**
     * Update all settings at once.
     */
    public function update(Request $request)
    {
        // Expecting an array: settings[university_name] = "MIT"
        $data = $request->input('settings', []);

        foreach ($data as $key => $value) {
            // Handle boolean checkboxes not sending "false"
            // (You might need specific logic here depending on how your frontend sends checkboxes)
            
            SystemConfig::set($key, $value);
        }

        // Handle File Uploads (Logos) separately if needed
        // if ($request->hasFile('site_logo')) ...

        return redirect()->back()->with('success', 'System settings updated.');
    }
}