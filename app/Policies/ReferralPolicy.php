<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\Referral;
use App\Models\User;

class ReferralPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionType::ManageRehabilitation->value)
            || $user->can(PermissionType::ViewRehabilitation->value);
    }

    public function view(User $user, Referral $referral): bool
    {
        return $user->can(PermissionType::ManageRehabilitation->value)
            || $user->can(PermissionType::ViewRehabilitation->value);
    }

    public function create(User $user): bool
    {
        return $user->can(PermissionType::ManageRehabilitation->value);
    }

    public function update(User $user, Referral $referral): bool
    {
        return $user->can(PermissionType::ManageRehabilitation->value);
    }

    public function delete(User $user, Referral $referral): bool
    {
        // Hanya administrator (ditangani oleh Gate::before)
        return false;
    }

    public function restore(User $user, Referral $referral): bool
    {
        // Hanya administrator (ditangani oleh Gate::before)
        return false;
    }

    public function forceDelete(User $user, Referral $referral): bool
    {
        // Hanya administrator (ditangani oleh Gate::before)
        return false;
    }
}
