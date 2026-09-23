<?php

namespace App\Services\Knowledge;

use App\Models\KnowledgeBase;

class KnowledgeBaseService
{
    public function queryForUser(int $userId)
    {
        return KnowledgeBase::query()->where("user_id", $userId);
    }
}
