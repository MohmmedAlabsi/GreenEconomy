<?php

namespace App\Services\FieldVisit;

use App\Models\FieldVisitReport;

class FieldVisitReportService
{
    public function queryForUser(int $userId)
    {
        return FieldVisitReport::query()->where("user_id", $userId);
    }
}
