<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos', 'pimpinan', 'pejabat_penandatangan']);
    }

    public function view(User $user, Client $client): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos', 'pimpinan', 'pejabat_penandatangan']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos']);
    }

    public function update(User $user, Client $client): bool
    {
        return $user->hasAnyRole(['administrator', 'petugas_dinsos']);
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->hasRole('administrator');
    }

    public function restore(User $user, Client $client): bool
    {
        return $user->hasRole('administrator');
    }

    public function forceDelete(User $user, Client $client): bool
    {
        return $user->hasRole('administrator');
    }
}
