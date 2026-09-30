<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\DtsenPurpose;
use App\Models\User;

class DtsenPurposePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, DtsenPurpose $dtsenPurpose): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can(PermissionType::ManageDtsenPurposes->value);
    }

    public function update(User $user, DtsenPurpose $dtsenPurpose): bool
    {
        return $user->can(PermissionType::ManageDtsenPurposes->value);
    }

    public function delete(User $user, DtsenPurpose $dtsenPurpose): bool
    {
        return $user->can(PermissionType::ManageDtsenPurposes->value);
    }
}
