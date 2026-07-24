<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiseaseTreatment;

class DiseaseTreatmentSeeder extends Seeder
{
    public function run(): void
    {
        DiseaseTreatment::factory(20)->create();
    }
}