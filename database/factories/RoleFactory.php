<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    /**
     * `roles` is the Spatie permission table: id, name, guard_name, timestamps
     * (there is no `slug` column). The guard matches RoleSeeder.
     */
    public function definition(): array
    {
        $roles = [
            'Administrator',
            'Farmer',
            'Agricultural Expert',
            'Industrial Investor',
            'Researcher',
            'Government Officer',
            'Content Manager',
            'Technical Support',
        ];

        return [
            'name'       => fake()->unique()->randomElement($roles),
            'guard_name' => 'api',
        ];
    }
}
