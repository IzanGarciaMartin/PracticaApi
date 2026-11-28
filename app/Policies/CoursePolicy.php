<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use App\Models\Academy;

class CoursePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Course $course): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Academy $academy): bool
    {
        return $user->id === $academy->user_id
            || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Course $course): bool
    {
        return $user->id === $course->academy->user_id
            || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Course $course): bool
    {
        return $user->id === $course->academy->user_id
            || $user->isAdmin();
    }

    public function viewStudents(User $user, Course $course): bool
    {
        return $user->id === $course->academy->user_id
            || $user->isAdmin();
    }
}
