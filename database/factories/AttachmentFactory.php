<?php

namespace Database\Factories;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttachmentFactory extends Factory
{
    protected $model = Attachment::class;

    public function definition(): array
    {
        return [
            'attachable_type' => fake()->randomElement([
                'App\Models\Plant',
                'App\Models\Consultation',
                'App\Models\FeasibilityStudy',
            ]),

            'attachable_id' => fake()->numberBetween(1, 20),

            'file_path' => fake()->filePath(),

            'file_type' => fake()->randomElement([
                'pdf',
                'jpg',
                'png',
                'docx',
            ]),

            'created_at' => now(),

            'updated_at' => now(),
        ];
    }
}