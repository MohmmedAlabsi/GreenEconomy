<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plant;

class PlantSeeder extends Seeder
{
    public function run(): void
    {
        Plant::factory(20)->create();
    }
}