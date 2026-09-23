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

            // Local storage path (same convention as AttachmentFactory) instead of random fake domains.
            'media_url' => fn (array $attributes) => '/storage/knowledge-base/'
                . fake()->uuid()
                . ($attributes['type'] === 'video' ? '.mp4' : '.pdf'),

            'file_size_bytes' => fake()->numberBetween(
                10000,
                5000000
            ),

            // knowledge_base_items.user_id is NOT NULL (the author of the item)
            'user_id' => User::query()->inRandomOrder()->value('id') ?? User::factory(),

            // NOTE: `view_count` was removed - the column does not exist in the migration.

            'created_at' => now(),

            'updated_at' => now(),
        ];
    }
}
