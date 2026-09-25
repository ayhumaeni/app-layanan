<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\WorkUnit;

class WorkUnitPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, WorkUnit $workUnit): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('administrator');
    }

    public function update(User $user, WorkUnit $workUnit): bool
    {
        return $user->hasRole('administrator');
    }

    public function delete(User $user, WorkUnit $workUnit): bool
    {
        return $user->hasRole('administrator');
    }

    public function restore(User $user, WorkUnit $workUnit): bool
    {
        return $user->hasRole('administrator');
    }

    public function forceDelete(User $user, WorkUnit $workUnit): bool
    {
        return $user->hasRole('administrator');
    }
}
