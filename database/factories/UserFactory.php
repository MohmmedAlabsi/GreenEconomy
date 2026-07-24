<?php

namespace Database\Factories;

use App\Models\Region;
use App\Models\Role;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [

            'name' => fake()->name(),

            'phone' => '77' . fake()->unique()->numerify('#######'),

            'email' => fake()->unique()->safeEmail(),

            'email_verified_at' => now(),

            'password' => static::$password ??= Hash::make('password'),

            'avatar' => null,

            'governorate' => fake()->randomElement([
                'صنعاء',
                'عدن',
                'تعز',
                'إب',
                'الحديدة',
            ]),

            'district' => fake()->city(),

            'crop_types' => fake()->randomElement([
                'Coffee',
                'Wheat',
                'Corn',
                'Vegetables',
                null,
            ]),

            'membership_tier' => fake()->randomElement([
                'standard',
                'premium',
            ]),

            'status' => fake()->randomElement([
                'active',
                'inactive',
            ]),

            'identity_verified' => fake()->boolean(80),

            'role_id' => Role::query()->inRandomOrder()->value('id'),

            'region_id' => Region::query()->inRandomOrder()->value('id'),

            'specialization_id' => Specialization::query()->inRandomOrder()->value('id'),

            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the user's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn () => [
            'email_verified_at' => null,
        ]);
    }
}