<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ClientCategory;
use App\Models\User;

class ClientCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ClientCategory $clientCategory): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('administrator');
    }

    public function update(User $user, ClientCategory $clientCategory): bool
    {
        return $user->hasRole('administrator');
    }

    public function delete(User $user, ClientCategory $clientCategory): bool
    {
        return $user->hasRole('administrator');
    }
}
