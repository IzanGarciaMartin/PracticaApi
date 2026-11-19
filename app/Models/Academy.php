<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description', 'address', 'phone', 'email'])]
class Academy extends Model
{
    /**
     * Get the user that owns the academy.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the courses belonging to the academy.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}
