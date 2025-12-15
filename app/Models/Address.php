<?php

namespace App\Models;

use App\Models\Location\Village; // Import the external model
use App\Models\Village as ModelsVillage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'addressable_id',
        'addressable_type',
        'current_address',
        'postal_code',
        'village_id', // This ID lives in the other database
    ];

    public function addressable()
    {
        return $this->morphTo();
    }

    /**
     * Relationship to the Village in the external DB.
     * Eloquent handles cross-database relationships automatically 
     * as long as the models define their connections correctly.
     */
    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id', 'id');
    }
    
    // Helper to display the full address easily
    public function getFullTextAttribute()
    {
        $base = $this->current_address;
        
        if ($this->village) {
            $v = $this->village;
            $c = $v->commune;
            $d = $c->district;
            $p = $d->province;
            
            // Format: #123 St 456, Village, Commune, District, Province
            return "{$base}, {$v->name_en}, {$c->name_en}, {$d->name_en}, {$p->name_en}";
        }
        
        return $base;
    }
}