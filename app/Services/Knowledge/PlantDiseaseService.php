<?php

namespace App\Services\Knowledge;

use App\Models\PlantDisease;

class PlantDiseaseService
{
    public function queryForUser(int $userId)
    {
        return PlantDisease::query()->where("user_id", $userId);
    }
}
