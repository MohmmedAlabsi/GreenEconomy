<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FeasibilityStudy;

class FeasibilityStudySeeder extends Seeder
{
    public function run(): void
    {
        FeasibilityStudy::factory(20)->create();
    }
}