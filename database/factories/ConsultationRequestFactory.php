<?php

namespace Database\Factories;

use App\Models\ConsultationRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsultationRequestFactory extends Factory
{
    protected $model = ConsultationRequest::class;

    public function definition(): array
    {
        return [

            // صاحب طلب الاستشارة
            'user_id' => User::inRandomOrder()->first()->id,

            // المستشار (نتركه فارغ حاليًا)
            'assigned_consultant_id' => null,

            'subject' => fake()->randomElement([
                'Plant disease consultation',
                'Green agriculture advice',
                'Crop production problem',
                'Irrigation system consultation',
                'Sustainable farming question',
            ]),

            'status' => fake()->randomElement([
                'pending',
                'approved',
                'completed',
                'rejected',
            ]),

            'created_at' => now(),

            'updated_at' => now(),
        ];
    }
}