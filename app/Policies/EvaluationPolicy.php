<?php

namespace App\Policies;

use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EvaluationPolicy
{
    private function isTrainerOrAdmin(User $user): bool
    {
        return in_array($user->employee->role ?? null, ['admin', 'trainer']);
    }

    private function isAdmin(User $user): bool
    {
        return in_array($user->employee->role ?? null, ['admin']);
    }

    public function viewAny(User $user): bool
    {
        return $this->isTrainerOrAdmin($user);
    }

    public function view(User $user, Evaluation $evaluation): bool
    {
        return $this->isTrainerOrAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->isTrainerOrAdmin($user);
    }

    public function update(User $user, Evaluation $evaluation): bool
    {
        return $this->isTrainerOrAdmin($user);
    }

    public function delete(User $user, Evaluation $evaluation): bool
    {
        return $this->isTrainerOrAdmin($user);
    }

    public function restore(User $user, Evaluation $evaluation): bool
    {
        return $this->isTrainerOrAdmin($user);
    }

    public function forceDelete(User $user, Evaluation $evaluation): bool
    {
        return $this->isTrainerOrAdmin($user);
    }

    public function viewCriteria(User $user): bool
    {
        return $this->isAdmin($user);
    }
}
