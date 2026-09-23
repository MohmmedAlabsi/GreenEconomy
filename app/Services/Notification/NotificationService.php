<?php

namespace App\Services\Notification;

use App\Models\Notification;

class NotificationService
{
    public function queryForUser(int $userId)
    {
        return Notification::query()->where("user_id", $userId);
    }
}
