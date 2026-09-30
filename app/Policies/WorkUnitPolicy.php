<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionType;
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
        return $user->can(PermissionType::ManageWorkUnits->value);
    }

    public function update(User $user, WorkUnit $workUnit): bool
    {
        return $user->can(PermissionType::ManageWorkUnits->value);
    }

    public function delete(User $user, WorkUnit $workUnit): bool
    {
        return $user->can(PermissionType::ManageWorkUnits->value);
    }

    public function restore(User $user, WorkUnit $workUnit): bool
    {
        return $user->can(PermissionType::ManageWorkUnits->value);
    }

    public function forceDelete(User $user, WorkUnit $workUnit): bool
    {
        return $user->can(PermissionType::ManageWorkUnits->value);
    }
}
