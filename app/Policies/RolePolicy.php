<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('administrator') || $user->can(PermissionType::ManageUsers->value);
    }

    public function view(User $user, Role $role): bool
    {
        return $user->hasRole('administrator') || $user->can(PermissionType::ManageUsers->value);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('administrator') || $user->can(PermissionType::ManageUsers->value);
    }

    public function update(User $user, Role $role): bool
    {
        return $user->hasRole('administrator') || $user->can(PermissionType::ManageUsers->value);
    }

    public function delete(User $user, Role $role): bool
    {
        // Jangan izinkan penghapusan role bawaan sistem
        $systemRoles = [
            'administrator',
            'petugas_dinsos',
            'pejabat_penandatangan',
            'pimpinan',
            'operator_kecamatan_desa',
            'masyarakat',
        ];

        if (in_array($role->name, $systemRoles, true)) {
            return false;
        }

        return $user->hasRole('administrator') || $user->can(PermissionType::ManageUsers->value);
    }
}
