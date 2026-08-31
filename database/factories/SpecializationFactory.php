<?php

namespace Database\Factories;

use App\Models\Specialization;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpecializationFactory extends Factory
{
    protected $model = Specialization::class;

    public function definition(): array
    {
        return [

            'role_id' => Role::inRandomOrder()->first()->id,

            'name' => fake()->randomElement([
             'تربة ومياة',
             'افات وامراض النبات', 
             'زراعة بدون تربة',  
             'انتاج محاصيل حقلية', 
             'انتاج محاصيل بستانية',  
             'مختص البن ومحاصيل نقدية',  
             'مختص ادارة مشاريع',  
             'مختص ارشاد زراعي',  
             'مختص ثروة حيوانية',  
             'مختص تصميم وانشاء شبكات ري',  
             'مختص زرعة عضوية'
            ]),

            'created_at' => now(),

            'updated_at' => now(),
        ];
    }
}