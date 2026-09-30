<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\RehabilitationCase;
use App\Models\User;

class RehabilitationCasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionType::ManageRehabilitation->value)
            || $user->can(PermissionType::ViewRehabilitation->value);
    }

    public function view(User $user, RehabilitationCase $case): bool
    {
        if ($user->can(PermissionType::ViewRehabilitation->value)) {
            return true;
        }

        if ($user->can(PermissionType::ManageRehabilitation->value)) {
            if ($user->hasRole('petugas_dinsos')) {
                return $case->officer_id === $user->id || $case->officer_id === null;
            }

            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can(PermissionType::ManageRehabilitation->value);
    }

    public function update(User $user, RehabilitationCase $case): bool
    {
        if ($user->can(PermissionType::ManageRehabilitation->value)) {
            if ($user->hasRole('petugas_dinsos')) {
                return $case->officer_id === $user->id || $case->officer_id === null;
            }

            return true;
        }

        return false;
    }

    public function delete(User $user, RehabilitationCase $case): bool
    {
        // Hanya administrator (ditangani oleh Gate::before)
        return false;
    }

    public function restore(User $user, RehabilitationCase $case): bool
    {
        // Hanya administrator (ditangani oleh Gate::before)
        return false;
    }

    public function forceDelete(User $user, RehabilitationCase $case): bool
    {
        // Hanya administrator (ditangani oleh Gate::before)
        return false;
    }
}
