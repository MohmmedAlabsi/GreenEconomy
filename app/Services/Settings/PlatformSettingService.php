<?php

namespace App\Services\Settings;

use App\Models\PlatformSetting;

class PlatformSettingService
{
    public function queryForUser(int $userId)
    {
        return PlatformSetting::query()->where("user_id", $userId);
    }
}
