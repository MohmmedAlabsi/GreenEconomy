<?php

namespace Database\Factories;

use App\Models\PlantDisease;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlantDiseaseFactory extends Factory
{
    protected $model = PlantDisease::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'اللفحة المتأخرة',
                'البياض الدقيقي',
                'عفن الجذور الفطري',
                'الذبول البكتيري',
                'صدأ الأوراق',
            ]),

            'scientific_name' => fake()->randomElement([
                'Phytophthora infestans',
                'Erysiphe spp.',
                'Fusarium oxysporum',
                'Pythium spp.',
                'Xanthomonas spp.',
            ]),

            'plant_type' => fake()->randomElement(['طماطم', 'بطاطس', 'خيار', 'قُمح', 'عنب']),

            'type' => fake()->randomElement(['فطري', 'بكتيري', 'فيروسي', 'حشري']),

            'severity_level' => fake()->randomElement(['منخفض', 'متوسط', 'عالي']),

            'spread_rate' => fake()->randomElement(['بطيء', 'متوسط', 'سريع']),

            'farmer_visibility' => fake()->randomElement(['مرئي للمزارعين', 'مخفي']),

            'symptoms' => fake()->paragraph(),

            'cause_description' => fake()->paragraph(),

            'image_url' => 'https://picsum.photos/640/480',

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}