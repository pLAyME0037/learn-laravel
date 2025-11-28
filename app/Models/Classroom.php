<?php

declare (strict_types = 1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classroom extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'capacity',
        'room_number',
        'building_name',
        'type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'capacity' => 'integer',
    ];

    /**
     * Get the class schedules for the classroom.
     */
    public function classSchedules()
    {
        return $this->hasMany(ClassSchedule::class, 'room_number', 'room_number');
    }

    /**
     * Get a status based on capacity.
     */
    public function getCapacityStatusAttribute(): string
    {
        if ($this->capacity < 30) {
            return 'Small';
        } elseif ($this->capacity >= 30 && $this->capacity <= 60) {
            return 'Medium';
        } else {
            return 'Large';
        }
    }

    /**
     * Scope a query to filter classrooms by a capacity range.
     */
    public function scopeByCapacity(Builder $query, int $minCapacity, int $maxCapacity): void
    {
        $query->whereBetween('capacity', [$minCapacity, $maxCapacity]);
    }

    /**
     * Scope a query to filter for classrooms available at a specific date and time.
     * Note: This is a basic check. A more robust implementation would involve detailed time slot checking.
     */
    public function scopeAvailable(Builder $query, string $date, string $time): void
    {
        $dateTime = Carbon::parse("{$date} {$time}");

        $query->whereDoesntHave('classSchedules', fn(Builder $subQuery) =>
            $subQuery->whereDate('schedule_date', $dateTime->toDateString())
                ->where(fn($q) =>
                    $q->where('start_time', '<', $dateTime->format('H:i:s'))
                        ->where('end_time', '>', $dateTime->format('H:i:s'))
                )
        );
    }
}
