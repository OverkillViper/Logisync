<?php

namespace App\Policies;

use App\Models\User;

class AttendancePolicy
{
    /**
     * Create a new policy instance.
     */
    public function view(User $user): bool
    {
        $employee = $user->employee;

        // Allow Admins and Trainers
        return in_array($employee->role, ['admin', 'trainer']);
    }
}
