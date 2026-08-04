<?php

namespace Database\Factories;

use App\Models\Consultation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsultationFactory extends Factory
{
    protected $model = Consultation::class;

    public function definition(): array
    {
        return [
            'user_id' => User::query()->inRandomOrder()->value('id'),

            'issue_title' => fake()->sentence(),

            'crop_type' => fake()->randomElement([
                'Tomato',
                'Wheat',
                'Coffee',
                'Corn',
                'Date Palm',
            ]),

            'crop_age' => fake()->numberBetween(1, 20),

            'issue_duration' => fake()->randomElement([
                '1 week',
                '2 weeks',
                '1 month',
                '3 months',
            ]),

            'description' => fake()->paragraph(),

            'status' => fake()->randomElement([
                'pending',
                'approved',
                'completed',
                'rejected',
            ]),

            'assigned_expert_id' => function (array $attributes) {
                return User::where('id', '!=', $attributes['user_id'])
                    ->inRandomOrder()
                    ->value('id')
                    ?? $attributes['user_id'];
            },

            'created_at' => now(),

            'updated_at' => now(),
        ];
    }
}