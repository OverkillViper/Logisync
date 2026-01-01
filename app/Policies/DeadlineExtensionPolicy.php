<?php

namespace App\Policies;

use App\Models\DeadlineExtension;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DeadlineExtensionPolicy
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
    public function view(User $user, DeadlineExtension $extension)
    {
        $employee = $user->employee;

        // Admin and trainer can view
        return in_array($employee->role, ['admin', 'trainer']);
    }

    public function cancel(User $user, DeadlineExtension $extension)
    {
        $employee = $user->employee;

        // Only the requesting trainee can cancel pending requests
        return $extension->requester->id === $employee->id && $extension->status === 'pending';
    }

    public function approve(User $user, DeadlineExtension $extension)
    {
        $employee = $user->employee;

        // Admin or reviewers can approve
        return $employee->role === 'admin' || $extension->project->reviewers->contains($employee);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DeadlineExtension $deadlineExtension): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DeadlineExtension $deadlineExtension): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DeadlineExtension $deadlineExtension): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DeadlineExtension $deadlineExtension): bool
    {
        return false;
    }
}
