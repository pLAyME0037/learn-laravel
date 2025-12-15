<?php
namespace App\Models\Location;

use App\Models\Location\Commune;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    protected $connection = 'sqlite_locations';
    public $timestamps    = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'villages';

    /**
     * Get the commune that owns the village.
     */
    public function commune()
    {
        return $this->belongsTo(Commune::class, 'commune_id', 'comm_id');
    }

    // Helper to get full address string
    public function getFullNameAttribute()
    {
        return "{$this->name_kh},
        {$this->commune->name_kh},
        {$this->commune->district->name_kh},
        {$this->commune->district->province->name_kh}";
    }
}
