<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\Complaint;
use App\Models\User;

class ComplaintPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionType::ManageComplaints->value)
            || $user->can(PermissionType::ViewComplaints->value)
            || $user->can(PermissionType::SubmitComplaints->value);
    }

    public function view(User $user, Complaint $complaint): bool
    {
        if ($user->can(PermissionType::ManageComplaints->value)
            || $user->can(PermissionType::ViewComplaints->value)) {
            if ($user->hasRole('operator_kecamatan_desa')) {
                return $this->isInUserTerritory($user, $complaint);
            }

            return true;
        }

        if ($user->hasRole('masyarakat')) {
            return $complaint->reporter_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can(PermissionType::ManageComplaints->value)
            || $user->can(PermissionType::SubmitComplaints->value);
    }

    public function update(User $user, Complaint $complaint): bool
    {
        if (! $user->can(PermissionType::ManageComplaints->value)) {
            return false;
        }

        if ($user->hasRole('operator_kecamatan_desa')) {
            return $this->isInUserTerritory($user, $complaint);
        }

        return true;
    }

    public function delete(User $user, Complaint $complaint): bool
    {
        // Hanya administrator (ditangani oleh Gate::before)
        return false;
    }

    public function restore(User $user, Complaint $complaint): bool
    {
        // Hanya administrator (ditangani oleh Gate::before)
        return false;
    }

    public function forceDelete(User $user, Complaint $complaint): bool
    {
        // Hanya administrator (ditangani oleh Gate::before)
        return false;
    }

    private function isInUserTerritory(User $user, Complaint $complaint): bool
    {
        if ($user->village_id && $complaint->village_id === $user->village_id) {
            return true;
        }

        if ($user->district_id && $complaint->village?->district_id === $user->district_id) {
            return true;
        }

        return false;
    }
}
