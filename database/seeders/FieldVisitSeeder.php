<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FieldVisit;

class FieldVisitSeeder extends Seeder
{
    public function run(): void
    {
        FieldVisit::factory(20)->create();
    }
}