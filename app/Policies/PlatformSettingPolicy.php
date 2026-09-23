<?php

namespace App\Policies;

use App\Models\PlatformSetting;
use App\Models\User;

class PlatformSettingPolicy
{
    public function viewAny(User $user): bool { return $user->hasPermission('settings.manage'); }
    public function view(User $user, PlatformSetting $setting): bool { return $user->hasPermission('settings.manage'); }
    public function update(User $user, PlatformSetting $setting): bool { return $user->hasPermission('settings.manage'); }
}
