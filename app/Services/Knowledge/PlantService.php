<?php

namespace App\Services\Knowledge;

use App\Models\Plant;

class PlantService
{
    public function queryForUser(int $userId)
    {
        return Plant::query()->where("user_id", $userId);
    }
}
