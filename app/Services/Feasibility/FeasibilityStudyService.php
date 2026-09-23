<?php

namespace App\Services\Feasibility;

use App\Models\FeasibilityStudy;

class FeasibilityStudyService
{
    public function queryForUser(int $userId)
    {
        return FeasibilityStudy::query()->where("user_id", $userId);
    }
}
