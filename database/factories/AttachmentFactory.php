<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\User;
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

            'file_path' => 'attachments/' . fake()->uuid() . '.' . fake()->randomElement(['pdf','jpg','png','docx']),

            'file_type' => fake()->randomElement([
                'pdf',
                'jpg',
                'png',
                'docx',
            ]),
            'user_id' => User::query()->inRandomOrder()->value('id'),
            'file_name' => fake()->word() . '.' . fake()->randomElement(['pdf','jpg','png','docx']),
            'file_size' => fake()->numberBetween(1024, 2048000),
            'url' => '/storage/' . 'attachments/' . fake()->uuid() . '.' . fake()->randomElement(['pdf','jpg','png','docx']),

            'created_at' => now(),

            'updated_at' => now(),
        ];
    }
}