<?php

namespace App\Policies;

use App\Models\EngineerProfile;
use App\Models\User;

class EngineerProfilePolicy
{
    public function viewAny(User $user): bool { return $user->hasPermission('profiles.view-directory') || $user->hasPermission('profiles.manage-all'); }
    public function view(User $user, EngineerProfile $profile): bool { return $user->hasPermission('profiles.manage-all') || $user->hasPermission('profiles.view-directory') || ($profile->user_id === $user->id && $user->hasPermission('profiles.manage-own')); }
    public function create(User $user): bool { return $user->hasPermission('profiles.manage-own') || $user->hasPermission('profiles.manage-all'); }
    public function update(User $user, EngineerProfile $profile): bool { return $user->hasPermission('profiles.manage-all') || ($profile->user_id === $user->id && $user->hasPermission('profiles.manage-own')); }
    public function delete(User $user, EngineerProfile $profile): bool { return $user->hasPermission('profiles.manage-all'); }
}
