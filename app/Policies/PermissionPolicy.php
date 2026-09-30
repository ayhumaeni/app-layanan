<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class PermissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('administrator') || $user->can(PermissionType::ManageUsers->value);
    }

    public function view(User $user, Permission $permission): bool
    {
        return $user->hasRole('administrator') || $user->can(PermissionType::ManageUsers->value);
    }

    public function create(User $user): bool
    {
        // Permission diatur secara konsisten melalui PermissionType enum dan seeder
        return false;
    }

    public function update(User $user, Permission $permission): bool
    {
        return $user->hasRole('administrator') || $user->can(PermissionType::ManageUsers->value);
    }

    public function delete(User $user, Permission $permission): bool
    {
        // Permission sistem tidak boleh dihapus sembarangan
        return false;
    }
}
