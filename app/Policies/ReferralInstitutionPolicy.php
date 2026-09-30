<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\ReferralInstitution;
use App\Models\User;

class ReferralInstitutionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ReferralInstitution $referralInstitution): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can(PermissionType::ManageReferralInstitutions->value);
    }

    public function update(User $user, ReferralInstitution $referralInstitution): bool
    {
        return $user->can(PermissionType::ManageReferralInstitutions->value);
    }

    public function delete(User $user, ReferralInstitution $referralInstitution): bool
    {
        return $user->can(PermissionType::ManageReferralInstitutions->value);
    }
}
