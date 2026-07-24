<?php

namespace Database\Factories;

use App\Models\FeasibilityRequest;
use App\Models\User;
use App\Models\Category;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeasibilityRequestFactory extends Factory
{
    protected $model = FeasibilityRequest::class;

    public function definition(): array
    {
        return [

            'user_id' => User::inRandomOrder()->first()->id,

            'project_title' => fake()->randomElement([
                'Smart Green Farm Project',
                'Solar Irrigation System',
                'Organic Agriculture Project',
                'Sustainable Crop Production',
                'Greenhouse Farming Project',
            ]),

            'category_id' => Category::inRandomOrder()->first()?->id,

            'region_id' => Region::inRandomOrder()->first()?->id,

            'estimated_budget' => fake()->randomFloat(
                2,
                1000,
                50000
            ),

            'land_area' => fake()->randomFloat(
                2,
                1,
                100
            ),

            'description' => fake()->paragraph(),

            'status' => fake()->randomElement([
                'pending',
                'approved',
                'rejected',
                'completed',
            ]),

            'created_at' => now(),

            'updated_at' => now(),
        ];
    }
}