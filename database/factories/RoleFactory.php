<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RoleFactory extends Factory
{
    protected $model = Role::class;

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

        $name = fake()->unique()->randomElement($roles);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}