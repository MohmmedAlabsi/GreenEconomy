<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConsultationRequest;

class ConsultationRequestSeeder extends Seeder
{
    public function run(): void
    {
        ConsultationRequest::factory(20)->create();
    }
}