<?php

namespace Database\Factories;

use App\Models\EngineerProfile;
use App\Models\User;
use App\Models\Specialization;
use Illuminate\Database\Eloquent\Factories\Factory;

class EngineerProfileFactory extends Factory
{
    protected $model = EngineerProfile::class;

    public function definition(): array
    {
        return [
            'user_id'             => User::factory(),
            'specialization_id'   => Specialization::inRandomOrder()->first()?->id ?? Specialization::factory(),
            'years_of_experience' => $this->faker->numberBetween(1, 20),
            'bio'                 => $this->faker->paragraph(3),
            'cv_file'             => 'cvs/sample_' . $this->faker->uuid() . '.pdf',
        ];
    }
}