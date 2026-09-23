<?php

namespace App\Services\Knowledge;

use App\Models\Category;

class CategoryService
{
    public function queryForUser(int $userId)
    {
        return Category::query()->where("user_id", $userId);
    }
}
