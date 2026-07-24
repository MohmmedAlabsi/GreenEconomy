<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActivityLog;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        ActivityLog::factory()
            ->count(50)
            ->create();
    }
}