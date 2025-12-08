<?php
namespace App\Models;

use App\Models\Commune;
use App\Models\Province;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    protected $connection = 'sqlite_locations';
    public $timestamps    = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'districts';

    /**
     * Get the province that owns the district.
     */
    public function province() :BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id', 'prov_id');
    }

    /**
     * Get the communes for the district.
     */
    public function communes() :HasMany
    {
        return $this->hasMany(Commune::class, 'district_id', 'dist_id');
    }
}
