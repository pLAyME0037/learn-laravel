<?php
namespace App\Models\Location;

use App\Models\Location\District;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    protected $connection = 'sqlite_locations';
    public $timestamps    = false;
    protected $table      = 'provinces';

    /**
     * Get the districts for the province.
     */
    public function districts() :HasMany
    {
        return $this->hasMany(District::class, 'province_id');
    }
}
