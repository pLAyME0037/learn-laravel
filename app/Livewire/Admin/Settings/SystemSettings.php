<?php
namespace App\Livewire\Admin\Settings;

use App\Models\SystemConfig;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SystemSettings extends Component
{
    // The "Form" Model
    public $settings = [];

    // The Definition of what settings exist
    protected $schema = [
        'General'  => [
            'university_name' => ['label' => 'University Name', 'type' => 'text', 'default' => 'My University'],
            'university_code' => ['label' => 'University Code', 'type' => 'text', 'default' => 'UNI'],
            'site_logo_url'   => ['label' => 'Logo URL', 'type' => 'url', 'default' => ''],
        ],
        'Academic' => [
            'current_academic_year' => ['label' => 'Current Academic Year', 'type' => 'text', 'default' => '2025-2026'],
            'allow_registration'    => ['label' => 'Allow Student Registration', 'type' => 'boolean', 'default' => true],
            'max_credits_per_sem'   => ['label' => 'Max Credits Per Semester', 'type' => 'number', 'default' => 24],
        ],
        'Finance'  => [
            'currency_symbol' => ['label' => 'Currency Symbol', 'type' => 'text', 'default' => '$'],
            'cost_per_credit' => ['label' => 'Cost Per Credit', 'type' => 'number', 'default' => 50],
            'late_fee_amount' => ['label' => 'Late Payment Fee', 'type' => 'number', 'default' => 25],
        ],
    ];

    public function mount()
    {
        // 1. Load existing values from DB
        $dbSettings = SystemConfig::all()->pluck('value', 'key')->toArray();

        // 2. Map schema to $this->settings, filling defaults where DB is empty
        foreach ($this->schema as $group => $fields) {
            foreach ($fields as $key => $config) {
                // Determine value: DB > Default > Null
                $val = $dbSettings[$key] ?? $config['default'];

                // Handle Boolean casting for Checkboxes
                if ($config['type'] === 'boolean') {
                    $this->settings[$key] = filter_var($val, FILTER_VALIDATE_BOOLEAN);
                } else {
                    $this->settings[$key] = $val;
                }
            }
        }
    }

    public function save()
    {
        // Loop through settings array and save to DB
        foreach ($this->settings as $key => $value) {
            // Convert boolean back to string/int for storage if needed, or rely on model casting
            // Using SystemConfig model helper
            SystemConfig::set($key, $value);
        }

        session()->flash('success', 'System settings updated successfully.');

        // Optional: Dispatch event to clear frontend caches if you use them
        $this->dispatch('settings-updated');
    }

    #[Layout('layouts.app', ['header' => 'Global Configuration'])]
    public function render()
    {
        return view('livewire.admin.settings.system-settings', [
            'schema' => $this->schema,
        ]);
    }
}
