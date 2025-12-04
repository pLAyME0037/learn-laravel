<?php
namespace App\Models;

use App\Models\Commune;
use App\Models\Province;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $connection = 'sqlite_locations';
    public $timestamps    = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'district';

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
        'type',
        'province_id',
    ];

    /**
     * Get the province that owns the district.
     */
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    /**
     * Get the communes for the district.
     */
    public function communes()
    {
        return $this->hasMany(Commune::class, 'district_id');
    }
}
