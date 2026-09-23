<?php

namespace App\Services\FieldVisit;

use App\Models\FieldVisit;

class FieldVisitService
{
    public function queryForUser(int $userId)
    {
        return FieldVisit::query()->where("user_id", $userId);
    }
}
