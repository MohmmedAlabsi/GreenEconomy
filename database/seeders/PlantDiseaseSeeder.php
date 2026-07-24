<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlantDisease;

class PlantDiseaseSeeder extends Seeder
{
    public function run(): void
    {
        PlantDisease::factory(20)->create();
    }
}