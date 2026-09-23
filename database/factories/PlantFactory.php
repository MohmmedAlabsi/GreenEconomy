<?php

namespace Database\Factories;

use App\Models\Plant;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlantFactory extends Factory
{
    protected $model = Plant::class;

    public function definition(): array
    {
        return [

            'common_name' => fake()->randomElement([
                'Tomato',
                'Wheat',
                'Coffee',
                'Corn',
                'Date Palm',
                'Potato',
                'Cucumber',
            ]),

            'scientific_name' => fake()->randomElement([
                'Solanum lycopersicum',
                'Triticum aestivum',
                'Coffea arabica',
                'Zea mays',
                'Phoenix dactylifera',
            ]),

            'description' => fake()->paragraph(),

            'climate_requirements' => fake()->sentence(),

            'irrigation_schedule' => fake()->randomElement([
                'Daily irrigation',
                'Twice a week',
                'Weekly irrigation',
                'Depends on season',
            ]),

            'planting_season' => fake()->randomElement([
                'Spring',
                'Summer',
                'Autumn',
                'Winter',
            ]),

            // plants.category_id is NOT NULL and has no default
            'category_id' => Category::query()->inRandomOrder()->value('id') ?? Category::factory(),

            // Stable, reachable placeholder (fake()->imageUrl() points at the dead via.placeholder.com)
            'image_url' => 'https://picsum.photos/seed/' . fake()->uuid() . '/640/480',

            'created_at' => now(),

            'updated_at' => now(),
        ];
    }
}
