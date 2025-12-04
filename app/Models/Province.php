<?php
namespace App\Models;

use App\Models\District;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $connection = 'sqlite_locations';
    public $timestamps    = false;
    protected $table      = 'province';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'camdx_id',
        'name_kh',
        'name_en',
        'type',
    ];

    /**
     * Get the districts for the province.
     */
    public function districts()
    {
        return $this->hasMany(District::class, 'province_id');
    }
}
