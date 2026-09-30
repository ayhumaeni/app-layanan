<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionType;
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
        return $user->can(PermissionType::ManageServiceTypes->value);
    }

    public function update(User $user, ServiceType $serviceType): bool
    {
        return $user->can(PermissionType::ManageServiceTypes->value);
    }

    public function delete(User $user, ServiceType $serviceType): bool
    {
        return $user->can(PermissionType::ManageServiceTypes->value);
    }

    public function restore(User $user, ServiceType $serviceType): bool
    {
        return $user->can(PermissionType::ManageServiceTypes->value);
    }

    public function forceDelete(User $user, ServiceType $serviceType): bool
    {
        return $user->can(PermissionType::ManageServiceTypes->value);
    }
}
