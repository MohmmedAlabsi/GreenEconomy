<?php

namespace App\Services\User;

use App\Models\Role;

class RoleService
{
    public function queryForUser(int $userId)
    {
        return Role::query()->where("user_id", $userId);
    }
}
