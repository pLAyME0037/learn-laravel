<?php
namespace App\Models\Location;

use App\Models\Location\District;
use App\Models\Location\Village;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commune extends Model
{
    protected $connection = 'sqlite_locations';
    public $timestamps    = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'communes';

    public function district() :BelongsTo
    {
        // (Related Model, Foreign Key in this table, Local Key in other table)
        // Links 'district_id' (102) in this table to 'dist_id' (102) in districts table
        return $this->belongsTo(District::class, 'district_id', 'dist_id');
    }

    public function villages() :HasMany
    {
        return $this->hasMany(Village::class, 'commune_id', 'comm_id');
    }
}
