<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'name',
    'surname',
    'email',
    'phone',
    'date_of_birth',
])]
class Student extends Model
{
    /**
     * Get the courses in which the student is enrolled.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class)
            ->withPivot('enrolled_at')
            ->withTimestamps();
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
