<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instructor extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payscale',
        'faculty_id',
        'department_id',
        'user_id',
        'info',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payscale' => 'integer',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    public function classSchedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class, 'instructor_id');
    }

    /**
     * Get the contact detail associated with the instructor.
     */
    public function contactDetail(): HasOne
    {
        return $this->hasOne(ContactDetail::class);
    }

    /**
     * Returns the full name of the instructor from the associated User model.
     */
    public function getFullNameAttribute(): string
    {
        return $this->user->name ?? '';
    }

    /**
     * Returns the name of the associated department.
     */
    public function getDepartmentNameAttribute(): string
    {
        return $this->department->name ?? '';
    }

    /**
     * Scope a query to only include instructors by department.
     */
    public function scopeByDepartment(Builder $query, int $departmentId): void
    {
        $query->where('department_id', $departmentId);
    }

    /**
     * Scope a query to filter instructors by payscale range.
     */
    public function scopeByPayscaleRange(Builder $query, int $min, int $max): void
    {
        $query->whereBetween('payscale', [$min, $max]);
    }

    /**
     * Scope a query to filter instructors who are teaching courses.
     */
    public function scopeHasCourses(Builder $query): void
    {
        $query->has('courses');
    }

    /**
     * Assigns a course to the instructor.
     */
    public function assignCourse(Course $course): void
    {
        $this->courses()->save($course);
    }

    /**
     * Removes a course from the instructor.
     */
    public function removeCourse(Course $course): void
    {
        $this->courses()->where('id', $course->id)->delete();
    }

    /**
     * Returns the count of courses taught by the instructor.
     */
    public function getTotalCoursesTaught(): int
    {
        return $this->courses()->count();
    }
}
