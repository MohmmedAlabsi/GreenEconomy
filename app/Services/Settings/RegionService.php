<?php

namespace App\Services\Settings;

use App\Models\Region;

class RegionService
{
    public function queryForUser(int $userId)
    {
        return Region::query()->where("user_id", $userId);
    }
}
