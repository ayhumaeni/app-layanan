<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\ComplaintCategory;
use App\Models\User;

class ComplaintCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ComplaintCategory $complaintCategory): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can(PermissionType::ManageComplaintCategories->value);
    }

    public function update(User $user, ComplaintCategory $complaintCategory): bool
    {
        return $user->can(PermissionType::ManageComplaintCategories->value);
    }

    public function delete(User $user, ComplaintCategory $complaintCategory): bool
    {
        return $user->can(PermissionType::ManageComplaintCategories->value);
    }
}
