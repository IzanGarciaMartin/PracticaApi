<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'name',
    'description',
    'start_date',
    'end_date',
    'capacity',
])]
class Course extends Model
{
    /**
     * Get the academy that owns the course.
     */
    public function academy(): BelongsTo
    {
        return $this->belongsTo(Academy::class);
    }

    /**
     * Get the students enrolled in the course.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class)
            ->withPivot('enrolled_at')
            ->withTimestamps();
    }
}
