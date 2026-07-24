<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FeasibilityRequest;

class FeasibilityRequestSeeder extends Seeder
{
    public function run(): void
    {
        FeasibilityRequest::factory(20)->create();
    }
}