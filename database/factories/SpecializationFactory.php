<?php

namespace Database\Factories;

use App\Models\Specialization;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpecializationFactory extends Factory
{
    protected $model = Specialization::class;

    public function definition(): array
    {
        return [

            'role_id' => Role::inRandomOrder()->first()->id,

            'name' => fake()->randomElement([
                'Agricultural Engineer',
                'Plant Disease Specialist',
                'Irrigation Specialist',
                'Soil Specialist',
                'Green Agriculture Consultant',
                'Crop Production Expert',
            ]),

            'created_at' => now(),

            'updated_at' => now(),
        ];
    }
}