<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Consultation;

class ConsultationSeeder extends Seeder
{
    public function run(): void
    {
        Consultation::factory(20)->create();
    }
}