<?php

namespace App\Services\FieldVisit;

use App\Models\FieldVisitReport;

class FieldVisitReportService
{
    public function queryForUser(int $userId)
    {
        return FieldVisitReport::query()->where('user_id', $userId);
    }

    public function query() { return FieldVisitReport::query()->with(['fieldVisit', 'engineer']); }
    public function save(array $data, int $userId, ?string $visitId = null): FieldVisitReport
    {
        return FieldVisitReport::create(array_merge($data, ['user_id' => $userId, 'field_visit_id' => $visitId ?? ($data['field_visit_id'] ?? null)]));
    }
}
