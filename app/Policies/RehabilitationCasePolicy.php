<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\RehabilitationCase;
use App\Models\User;

class RehabilitationCasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos', 'pimpinan', 'pejabat_penandatangan']);
    }

    public function view(User $user, RehabilitationCase $case): bool
    {
        if ($user->hasAnyRole(['administrator', 'pimpinan', 'pejabat_penandatangan'])) {
            return true;
        }

        if ($user->hasRole('petugas_dinsos')) {
            return $case->officer_id === $user->id || $case->officer_id === null;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos']);
    }

    public function update(User $user, RehabilitationCase $case): bool
    {
        if ($user->hasRole('administrator')) {
            return true;
        }

        if ($user->hasRole('petugas_dinsos')) {
            return $case->officer_id === $user->id || $case->officer_id === null;
        }

        return false;
    }

    public function delete(User $user, RehabilitationCase $case): bool
    {
        return $user->hasRole('administrator');
    }

    public function restore(User $user, RehabilitationCase $case): bool
    {
        return $user->hasRole('administrator');
    }

    public function forceDelete(User $user, RehabilitationCase $case): bool
    {
        return $user->hasRole('administrator');
    }
}
