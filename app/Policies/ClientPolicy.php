<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionType::ManageClients->value)
            || $user->can(PermissionType::ViewRehabilitation->value);
    }

    public function view(User $user, Client $client): bool
    {
        return $user->can(PermissionType::ManageClients->value)
            || $user->can(PermissionType::ViewRehabilitation->value);
    }

    public function create(User $user): bool
    {
        return $user->can(PermissionType::ManageClients->value);
    }

    public function update(User $user, Client $client): bool
    {
        return $user->can(PermissionType::ManageClients->value);
    }

    public function delete(User $user, Client $client): bool
    {
        // Hanya administrator (ditangani oleh Gate::before)
        return false;
    }

    public function restore(User $user, Client $client): bool
    {
        // Hanya administrator (ditangani oleh Gate::before)
        return false;
    }

    public function forceDelete(User $user, Client $client): bool
    {
        // Hanya administrator (ditangani oleh Gate::before)
        return false;
    }
}
