<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Complaint;
use App\Models\User;

class ComplaintPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            'administrator',
            'petugas_dinsos',
            'pimpinan',
            'operator_kecamatan_desa',
            'masyarakat',
        ]);
    }

    public function view(User $user, Complaint $complaint): bool
    {
        if ($user->hasAnyRole(['administrator', 'petugas_dinsos', 'pimpinan'])) {
            return true;
        }

        if ($user->hasRole('operator_kecamatan_desa')) {
            if ($user->village_id && $complaint->village_id === $user->village_id) {
                return true;
            }
            if ($user->district_id && $complaint->village?->district_id === $user->district_id) {
                return true;
            }
        }

        if ($user->hasRole('masyarakat')) {
            return $complaint->reporter_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Complaint $complaint): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos']);
    }

    public function delete(User $user, Complaint $complaint): bool
    {
        return $user->hasRole('administrator');
    }

    public function restore(User $user, Complaint $complaint): bool
    {
        return $user->hasRole('administrator');
    }

    public function forceDelete(User $user, Complaint $complaint): bool
    {
        return $user->hasRole('administrator');
    }
}
