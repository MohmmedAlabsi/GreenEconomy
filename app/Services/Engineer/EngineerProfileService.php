<?php

namespace App\Services\Engineer;

use App\Models\EngineerProfile;

class EngineerProfileService
{
    public function queryForUser(int $userId)
    {
        return EngineerProfile::query()->where("user_id", $userId);
    }
}
