<?php
namespace App\Livewire\Forms;

use App\Models\Location\Commune;
use App\Models\Location\District;
use App\Models\Location\Province;
use App\Models\Location\Village;
use Livewire\Component;

class LocationPicker extends Component
{
    // Selected IDs
    public $province_id;
    public $district_id;
    public $commune_id;
    public $village_id;

    // Data Lists
    public $provinces = [];
    public $districts = [];
    public $communes  = [];
    public $villages  = [];

    public $emitUp = true; // Tell parent component when selection changes

    public function mount($selectedVillageId = null)
    {
        // Initial Load: Provinces (Always needed)
        $this->provinces = Province::orderBy('name_en')->get();

        // If editing, reverse-engineer the dropdowns
        if ($selectedVillageId) {
            $this->village_id = $selectedVillageId;
            $village          = Village::find($selectedVillageId);

            if ($village) {
                $this->commune_id = $village->commune_id; // Note: using comm_id code relation
                $commune          = $village->commune;

                if ($commune) {
                    $this->district_id = $commune->district_id;
                    $district          = $commune->district;

                    if ($district) {
                        $this->province_id = $district->province_id;

                        // Load lists for the selected hierarchy
                        $this->updatedProvinceId($this->province_id);
                        $this->updatedDistrictId($this->district_id);
                        $this->updatedCommuneId($this->commune_id);
                    }
                }
            }
        }
    }

    public function updatedProvinceId($value)
    {
        $this->districts = District::where('province_id', $value)->orderBy('name_en')->get();
        $this->communes  = [];
        $this->villages  = [];
        $this->reset(['district_id', 'commune_id', 'village_id']);
    }

    public function updatedDistrictId($value)
    {
        $this->communes = Commune::where('district_id', $value)->orderBy('name_en')->get();
        $this->villages = [];
        $this->reset(['commune_id', 'village_id']);
    }

    public function updatedCommuneId($value)
    {
        $this->villages = Village::where('commune_id', $value)->orderBy('name_en')->get();
        $this->reset(['village_id']);
    }

    public function updatedVillageId($value)
    {
        if ($this->emitUp) {
            $this->dispatch('location-selected', village_id: $value);
        }
    }

    public function render()
    {
        return view('livewire.forms.location-picker');
    }
}
