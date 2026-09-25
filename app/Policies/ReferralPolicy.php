<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Referral;
use App\Models\User;

class ReferralPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos', 'pimpinan', 'pejabat_penandatangan']);
    }

    public function view(User $user, Referral $referral): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos', 'pimpinan', 'pejabat_penandatangan']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos']);
    }

    public function update(User $user, Referral $referral): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos']);
    }

    public function delete(User $user, Referral $referral): bool
    {
        return $user->hasRole('administrator');
    }

    public function restore(User $user, Referral $referral): bool
    {
        return $user->hasRole('administrator');
    }

    public function forceDelete(User $user, Referral $referral): bool
    {
        return $user->hasRole('administrator');
    }
}
