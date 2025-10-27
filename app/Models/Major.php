<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Major extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'major_name',
        'department_id',
        'degree_id',
        'major_cost',
        'payment_frequency',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'major_cost' => 'decimal:2',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function degree() {
        return $this->belongsTo(Degree::class);
    }
}
