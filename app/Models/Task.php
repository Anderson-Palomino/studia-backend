<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'title',
        'type',
        'priority',
        'duration',
        'deadline',
        'status',
        'energy',
        'description'
    ];

    protected $attributes = [
        'priority' => 'medium',
        'duration' => 60,
        'status' => 'todo',
        'energy' => 'medium',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    // Relación con User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relación con Course
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
