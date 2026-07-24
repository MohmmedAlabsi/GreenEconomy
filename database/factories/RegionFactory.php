<?php

namespace Database\Factories;

use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegionFactory extends Factory
{
    protected $model = Region::class;

    public function definition(): array
    {
        return [

            'name' => fake()->randomElement([
                'Sana’a',
                'Aden',
                'Taiz',
                'Ibb',
                'Hodeidah',
                'Dhamar',
                'Hadramout',
                'Amran',
                'Marib',
                'Al Mahwit',
            ]),

            'created_at' => now(),

            'updated_at' => now(),
        ];
    }
}