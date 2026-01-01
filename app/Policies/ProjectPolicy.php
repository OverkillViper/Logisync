<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        $employee = $user->employee;

        // Allow Admins and Trainers
        if (in_array($employee->role, ['admin', 'trainer'])) {
            return true;
        }

        // Allow if this employee is assigned to the project
        return $project->assignees()
            ->where('employees.id', $employee->id)
            ->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        $role = strtolower($user->employee->role);
        return in_array($role, ['trainer', 'admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        $role = strtolower($user->employee->role);
        return in_array($role, ['trainer', 'admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        $employee = $user->employee;

        // Allow Admins
        if (in_array($employee->role, ['admin'])) {
            return true;
        }

        // Allow if the user is the creator of the project
        if ($user->id === $project->created_by) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Project $project): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        return false;
    }
}
