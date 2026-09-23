<?php

namespace App\Services\FieldVisit;

use App\Models\Attachment;

class AttachmentService
{
    public function queryForUser(int $userId)
    {
        return Attachment::query()->where("user_id", $userId);
    }
}
