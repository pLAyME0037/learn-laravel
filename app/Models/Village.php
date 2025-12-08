<?php
namespace App\Models;

use App\Models\Commune;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    protected $connection = 'sqlite_locations';
    public $timestamps = false;
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
}
