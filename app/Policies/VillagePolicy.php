<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Village;

class VillagePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Village $village): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('administrator');
    }

    public function update(User $user, Village $village): bool
    {
        return $user->hasRole('administrator');
    }

    public function delete(User $user, Village $village): bool
    {
        return $user->hasRole('administrator');
    }
}
