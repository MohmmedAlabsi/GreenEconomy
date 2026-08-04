<?php

namespace Database\Factories;

use App\Models\KnowledgeBaseItem;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class KnowledgeBaseItemFactory extends Factory
{
    protected $model = KnowledgeBaseItem::class;

    public function definition(): array
    {
        return [

            'title' => fake()->randomElement([
                'Green Agriculture Basics',
                'Organic Farming Techniques',
                'Smart Irrigation Methods',
                'Plant Disease Prevention Guide',
                'Sustainable Farming Practices',
            ]),

            'summary' => fake()->sentence(),

            'content' => fake()->paragraphs(5, true),

            'type' => fake()->randomElement([
                'article',
                'guide',
                'video',
                'document',
            ]),

            'status' => fake()->randomElement([
                'published',
                'draft',
                'archived',
            ]),

            'category_id' => Category::inRandomOrder()->first()->id,

            'media_url' => fake()->url(),

            'file_size_bytes' => fake()->numberBetween(
                10000,
                5000000
            ),

            'view_count' => fake()->numberBetween(
                0,
                5000
            ),

            'user_id' => User::inRandomOrder()->first()->id,

            'created_at' => now(),

            'updated_at' => now(),
        ];
    }
}