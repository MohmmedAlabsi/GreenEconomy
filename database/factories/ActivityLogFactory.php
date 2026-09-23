<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        return [
            // Reuse an existing user instead of creating a brand-new one per log row.
            'user_id' => User::query()->inRandomOrder()->value('id') ?? User::factory(),

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

    /**
     * The activity_logs table has created_at only (no updated_at column), so the
     * factory must not let Eloquent try to write updated_at.
     */
    public function newModel(array $attributes = [])
    {
        $model = parent::newModel($attributes);
        $model->timestamps = false;

        return $model;
    }
}
