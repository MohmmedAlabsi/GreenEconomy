<?php

namespace App\Services\Knowledge;

use App\Models\DiseaseTreatment;

class DiseaseTreatmentService
{
    public function queryForUser(int $userId)
    {
        return DiseaseTreatment::query()->where("user_id", $userId);
    }
}
