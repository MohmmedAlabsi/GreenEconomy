<?php

namespace Database\Factories;

use App\Models\PlantDisease;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlantDiseaseFactory extends Factory
{
    protected $model = PlantDisease::class;

    public function definition(): array
    {
        return [

            'name' => fake()->randomElement([
                'Powdery Mildew',
                'Leaf Blight',
                'Root Rot',
                'Bacterial Wilt',
                'Rust Disease',
            ]),

            'scientific_name' => fake()->optional()->randomElement([
                'Erysiphe spp.',
                'Fusarium oxysporum',
                'Pythium spp.',
                'Xanthomonas spp.',
            ]),

            'type' => fake()->randomElement([
                'fungal',
                'bacterial',
                'viral',
                'environmental',
            ]),

            'symptoms' => fake()->paragraph(),

            'cause_description' => fake()->paragraph(),

            'image_url' => fake()->optional()->imageUrl(),

            'created_at' => now(),

            'updated_at' => now(),
        ];
    }
}