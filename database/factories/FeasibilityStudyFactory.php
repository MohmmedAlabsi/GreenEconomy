<?php

namespace Database\Factories;

use App\Models\FeasibilityStudy;
use App\Models\User;
use App\Models\Category;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeasibilityStudyFactory extends Factory
{
    protected $model = FeasibilityStudy::class;

    public function definition(): array
    {
        return [

            'title' => fake()->randomElement([
                'Smart Agriculture Investment Project',
                'Greenhouse Farming Feasibility Study',
                'Solar Powered Irrigation Project',
                'Organic Farming Business Plan',
                'Sustainable Crop Production Study',
            ]),

            'description' => fake()->paragraph(),

            'category_id' => Category::inRandomOrder()->first()?->id,

            'region_id' => Region::inRandomOrder()->first()?->id,

            'cover_image' => fake()->imageUrl(640, 480, 'business'),

            'capital_required' => fake()->randomFloat(
                2,
                5000,
                100000
            ),

            'expected_roi' => fake()->randomFloat(
                2,
                5,
                50
            ),

            'payback_period' => fake()->numberBetween(
                1,
                10
            ),

            'risk_level' => fake()->randomElement([
                'low',
                'medium',
                'high',
            ]),

            'status' => fake()->randomElement([
                'draft',
                'published',
                'approved',
                'rejected',
            ]),

            'pdf_file' => 'documents/' . fake()->uuid() . '.pdf',

            'user_id' => User::inRandomOrder()->first()->id,

            'created_at' => now(),

            'updated_at' => now(),
        ];
    }
}