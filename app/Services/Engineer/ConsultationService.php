<?php

namespace App\Services\Engineer;

use App\Models\Consultation;

class ConsultationService
{
    public function queryForUser(int $userId)
    {
        return Consultation::query()->where("user_id", $userId);
    }
}
