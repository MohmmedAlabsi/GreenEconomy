<?php

namespace App\Services\Feasibility;

use App\Models\FeasibilityRequest;

class FeasibilityRequestService
{
    public function queryForUser(int $userId)
    {
        return FeasibilityRequest::query()->where("user_id", $userId);
    }
}
