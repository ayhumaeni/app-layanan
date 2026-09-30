<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\ServiceRequest;
use App\Models\User;

class ServiceRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionType::ManageServiceRequests->value)
            || $user->can(PermissionType::ViewServiceRequests->value)
            || $user->can(PermissionType::SubmitServiceRequests->value);
    }

    public function view(User $user, ServiceRequest $serviceRequest): bool
    {
        if ($user->can(PermissionType::ManageServiceRequests->value)
            || $user->can(PermissionType::ViewServiceRequests->value)) {
            if ($user->hasRole('operator_kecamatan_desa')) {
                return $this->isInUserTerritory($user, $serviceRequest);
            }

            return true;
        }

        if ($user->hasRole('masyarakat')) {
            return $serviceRequest->submitter_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can(PermissionType::ManageServiceRequests->value)
            || $user->can(PermissionType::SubmitServiceRequests->value);
    }

    public function update(User $user, ServiceRequest $serviceRequest): bool
    {
        if ($user->can(PermissionType::ManageServiceRequests->value)
            || $user->can(PermissionType::SignDocuments->value)) {
            if ($user->hasRole('operator_kecamatan_desa')) {
                return $this->isInUserTerritory($user, $serviceRequest);
            }

            return true;
        }

        return false;
    }

    public function delete(User $user, ServiceRequest $serviceRequest): bool
    {
        // Hanya administrator (ditangani oleh Gate::before)
        return false;
    }

    public function restore(User $user, ServiceRequest $serviceRequest): bool
    {
        // Hanya administrator (ditangani oleh Gate::before)
        return false;
    }

    public function forceDelete(User $user, ServiceRequest $serviceRequest): bool
    {
        // Hanya administrator (ditangani oleh Gate::before)
        return false;
    }

    private function isInUserTerritory(User $user, ServiceRequest $serviceRequest): bool
    {
        if ($user->village_id && $serviceRequest->village_id === $user->village_id) {
            return true;
        }

        if ($user->district_id && $serviceRequest->village?->district_id === $user->district_id) {
            return true;
        }

        return false;
    }
}
