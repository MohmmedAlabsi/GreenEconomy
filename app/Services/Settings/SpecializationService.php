<?php

namespace App\Services\Settings;

use App\Models\Specialization;

class SpecializationService
{
    public function queryForUser(int $userId)
    {
        return Specialization::query()->where("user_id", $userId);
    }
}
