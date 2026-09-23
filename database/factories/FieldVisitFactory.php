<?php

namespace Database\Factories;

use App\Models\FieldVisit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FieldVisitFactory extends Factory
{
    protected $model = FieldVisit::class;

    public function definition(): array
    {
        return [

            'user_id' => User::inRandomOrder()->first()->id,

            'contact_name' => fake()->name(),

            // contact_phone is varchar(20); fake()->phoneNumber() can exceed that (e.g. "1-234-567-8901 x12345")
            'contact_phone' => '77' . fake()->numerify('#######'),

            'governorate' => fake()->randomElement([
                'Sana’a',
                'Aden',
                'Taiz',
                'Ibb',
                'Hodeidah',
            ]),

            'district' => fake()->city(),

            'village_or_area' => fake()->streetName(),

            'nearest_landmark' => fake()->sentence(),

            'crop_type' => fake()->randomElement([
                'Tomato',
                'Wheat',
                'Coffee',
                'Corn',
                'Date Palm',
            ]),

            'area_size' => fake()->randomFloat(
                2,
                1,
                200
            ),

            'infestation_type' => fake()->randomElement([
                'Insect infestation',
                'Fungal disease',
                'Bacterial infection',
                'Nutrient deficiency',
            ]),

            'priority_level' => fake()->randomElement([
                'low',
                'medium',
                'high',
                'urgent',
            ]),

            'problem_description' => fake()->paragraph(),

            'status' => fake()->randomElement([
                'submitted',
                'scheduled',
                'completed',
                'cancelled',
            ]),

            'scheduled_at' => fake()->dateTimeBetween(
                'now',
                '+1 month'
            ),

            'estimated_cost' => fake()->randomFloat(
                2,
                0,
                5000
            ),

            'created_at' => now(),

            'updated_at' => now(),
        ];
    }
}