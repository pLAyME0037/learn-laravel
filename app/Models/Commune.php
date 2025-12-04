<?php
namespace App\Models;

use App\Models\District;
use App\Models\Village;
use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
    protected $connection = 'sqlite_locations';
    public $timestamps    = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'commune';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'camdx_id',
        'code',
        'name_kh',
        'name_en',
        'district_id',
    ];

    /**
     * Get the district that owns the commune.
     */
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    /**
     * Get the villages for the commune.
     */
    public function villages()
    {
        return $this->hasMany(Village::class, 'commune_id');
    }
}
