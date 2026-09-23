<?php

namespace App\Services\User;

use App\Models\User;

class UserService
{
    public function queryForUser(int $userId)
    {
        return User::query()->where("user_id", $userId);
    }
}
