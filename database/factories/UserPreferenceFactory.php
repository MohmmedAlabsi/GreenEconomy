<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserPreference>
 */
class UserPreferenceFactory extends Factory
{
    protected $model = UserPreference::class;

    public function definition(): array
    {
        return [

            'user_id' => User::query()->inRandomOrder()->value('id'),

            'email_notifications_enabled' => fake()->boolean(90),

            'browser_notifications_enabled' => fake()->boolean(80),

            'sms_critical_alerts_enabled' => fake()->boolean(70),

            'two_factor_auth_enabled' => fake()->boolean(50),
        ];
    }
}