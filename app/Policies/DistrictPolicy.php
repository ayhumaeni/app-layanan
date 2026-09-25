<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\District;
use App\Models\User;

class DistrictPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, District $district): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('administrator');
    }

    public function update(User $user, District $district): bool
    {
        return $user->hasRole('administrator');
    }

    public function delete(User $user, District $district): bool
    {
        return $user->hasRole('administrator');
    }
}
