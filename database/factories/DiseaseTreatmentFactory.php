<?php

namespace Database\Factories;

use App\Models\DiseaseTreatment;
use App\Models\PlantDisease;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiseaseTreatmentFactory extends Factory
{
    protected $model = DiseaseTreatment::class;

    public function definition(): array
    {
        return [
            'disease_id' => PlantDisease::inRandomOrder()->first()->id,

            'treatment_type' => fake()->randomElement([
                'Organic',
                'Chemical',
                'Biological',
                'Preventive',
            ]),

            'title' => fake()->randomElement([
                'Natural pesticide treatment',
                'Fungicide application',
                'Soil improvement method',
                'Plant protection procedure',
            ]),

            'instructions' => fake()->paragraphs(3, true),

            'created_at' => now(),

            'updated_at' => now(),
        ];
    }
}