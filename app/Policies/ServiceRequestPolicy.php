<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ServiceRequest;
use App\Models\User;

class ServiceRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            'administrator',
            'petugas_dinsos',
            'pejabat_penandatangan',
            'pimpinan',
            'operator_kecamatan_desa',
            'masyarakat',
        ]);
    }

    public function view(User $user, ServiceRequest $serviceRequest): bool
    {
        if ($user->hasAnyRole(['administrator', 'petugas_dinsos', 'pejabat_penandatangan', 'pimpinan'])) {
            return true;
        }

        if ($user->hasRole('operator_kecamatan_desa')) {
            if ($user->village_id && $serviceRequest->village_id === $user->village_id) {
                return true;
            }
            if ($user->district_id && $serviceRequest->village?->district_id === $user->district_id) {
                return true;
            }
        }

        if ($user->hasRole('masyarakat')) {
            return $serviceRequest->submitter_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole([
            'administrator',
            'petugas_dinsos',
            'operator_kecamatan_desa',
            'masyarakat',
        ]);
    }

    public function update(User $user, ServiceRequest $serviceRequest): bool
    {
        if ($user->hasAnyRole(['administrator', 'petugas_dinsos', 'pejabat_penandatangan'])) {
            return true;
        }

        if ($user->hasRole('operator_kecamatan_desa')) {
            if ($user->village_id && $serviceRequest->village_id === $user->village_id) {
                return true;
            }
            if ($user->district_id && $serviceRequest->village?->district_id === $user->district_id) {
                return true;
            }
        }

        return false;
    }

    public function delete(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->hasRole('administrator');
    }

    public function restore(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->hasRole('administrator');
    }

    public function forceDelete(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->hasRole('administrator');
    }
}
