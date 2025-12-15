<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramStructure extends Model
{
    protected $table = 'program_structures';

    protected $fillable = [
        'program_id',
        'course_id',
        'recommended_year',
        'recommended_term', // 1 or 2
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Scope to sort the roadmap logically.
     * Usage: ProgramStructure::sorted()->get();
     */
    public function scopeSorted($query)
    {
        return $query->orderBy('recommended_year')
            ->orderBy('recommended_term');
    }

    // Helper accessor for display
    public function getTermLabelAttribute()
    {
        return "Year {$this->recommended_year}, Semester {$this->recommended_term}";
    }
}
