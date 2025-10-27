<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class CreditScore extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'course_id',
        'score',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'score' => 'integer',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get a status based on the score.
     */
    public function getScoreStatusAttribute(): string
    {
        if ($this->score > 80) {
            return 'High';
        } elseif ($this->score >= 60) {
            return 'Medium';
        } else {
            return 'Low';
        }
    }

    /**
     * Scope a query to only include credit scores by a specific student.
     */
    public function scopeByStudent(Builder $query, int $studentId): void
    {
        $query->where('student_id', $studentId);
    }

    /**
     * Scope a query to only include credit scores by a specific course.
     */
    public function scopeByCourse(Builder $query, int $courseId): void
    {
        $query->where('course_id', $courseId);
    }

    /**
     * Scope a query to filter for scores above a certain value.
     */
    public function scopeAboveScore(Builder $query, int $score): void
    {
        $query->where('score', '>', $score);
    }

    /**
     * Scope a query to filter for scores below a certain value.
     */
    public function scopeBelowScore(Builder $query, int $score): void
    {
        $query->where('score', '<', $score);
    }

    /**
     * Checks if the score is above a given threshold.
     */
    public function isHighScore(int $threshold): bool
    {
        return $this->score > $threshold;
    }
}
