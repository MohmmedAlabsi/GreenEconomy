<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ActivityLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),

            'action_text' => fake()->sentence(),

            'target_type' => fake()->randomElement([
                'Plant',
                'Consultation',
                'FeasibilityStudy'
            ]),

            'target_id' => fake()->numberBetween(1, 100),

            'created_at' => now(),
        ];
    }
}