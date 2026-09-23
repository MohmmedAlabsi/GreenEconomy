<?php

namespace Database\Factories;

use App\Models\Specialization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Permission\Models\Role;

class SpecializationFactory extends Factory
{
    protected $model = Specialization::class;

    /**
     * الدور الذي تتبع له التخصصات الثابتة (يُنشأ في RoleSeeder).
     */
    public const ROLE_NAME = 'Agricultural Expert';

    /**
     * التخصصات الثابتة للمنصة — المصدر الوحيد لهذه البيانات.
     * يقوم SpecializationSeeder بتخزينها في قاعدة البيانات كما هي،
     * ويستخدمها الـ Factory نفسه عند توليد بيانات تجريبية.
     */
    public const NAMES = [
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
        'مختص زرعة عضوية',
    ];

    public function definition(): array
    {
        return [

            // `roles` هو جدول Spatie الذي يملؤه RoleSeeder.
            'role_id' => Role::query()->where('name', self::ROLE_NAME)->value('id')
                ?? Role::query()->inRandomOrder()->value('id'),

            'name' => fake()->randomElement(self::NAMES),

            'created_at' => now(),

            'updated_at' => now(),
        ];
    }
}
