<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ServiceType;
use App\Models\User;

class ServiceTypePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ServiceType $serviceType): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('administrator');
    }

    public function update(User $user, ServiceType $serviceType): bool
    {
        return $user->hasRole('administrator');
    }

    public function delete(User $user, ServiceType $serviceType): bool
    {
        return $user->hasRole('administrator');
    }

    public function restore(User $user, ServiceType $serviceType): bool
    {
        return $user->hasRole('administrator');
    }

    public function forceDelete(User $user, ServiceType $serviceType): bool
    {
        return $user->hasRole('administrator');
    }
}
