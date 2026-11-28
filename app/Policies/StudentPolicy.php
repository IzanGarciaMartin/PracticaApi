<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
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
    public function view(User $user, Student $student): bool
    {
        return $this->canManage($user, $student);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Student $student): bool
    {
        return $this->canManage($user, $student);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Student $student): bool
    {
        return $this->canManage($user, $student);
    }

    /**
     * Check whether the user can manage the student.
     */
    private function canManage(User $user, Student $student): bool
    {
        // El administrador puede gestionar cualquier estudiante.
        if ($user->isAdmin()) {
            return true;
        }

        // El creador puede gestionar su estudiante.
        if ($user->id === $student->user_id) {
            return true;
        }

        // También puede gestionarlo el propietario de una academia
        // donde el estudiante esté matriculado.
        return $student->courses()
            ->whereHas('academy', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->exists();
    }
}
