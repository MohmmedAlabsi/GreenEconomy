<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // تعطيل فحص المفاتيح الأجنبية لحذف المحتوى القديم بأمان
        Schema::disableForeignKeyConstraints();

        // حذف كافة السجلات القديمة وتصفير الترقيم التلقائي (Truncate)
        Category::truncate();

        // إعادة تفعيل فحص المفاتيح الأجنبية
        Schema::enableForeignKeyConstraints();

        $categories = [
            [
                'name' => 'جدوى الإنتاج النباتي',
                'slug' => 'plant-production',
                'type' => 'feasibility_study',
            ],
            [
                'name' => 'جدوى الثروة الحيوانية',
                'slug' => 'livestock-production',
                'type' => 'feasibility_study',
            ],
            [
                'name' => 'جدوى الاستزراع المائي والمناحل',
                'slug' => 'aquaculture-and-beekeeping',
                'type' => 'feasibility_study',
            ],
            [
                'name' => 'جدوى المشاتل والتنسيق الزراعي',
                'slug' => 'nurseries-and-landscaping',
                'type' => 'feasibility_study',
            ],
            [
                'name' => 'جدوى التصنيع الغذائي الزراعي',
                'slug' => 'agro-food-processing',
                'type' => 'feasibility_study',
            ],
            [
                'name' => 'جدوى التخزين والتبريد',
                'slug' => 'storage-and-cold-chain',
                'type' => 'feasibility_study',
            ],
            [
                'name' => 'جدوى أنظمة الري والزراعة المحمية',
                'slug' => 'irrigation-and-greenhouses',
                'type' => 'feasibility_study',
            ],
            [
                'name' => 'جدوى التسويق والتصدير الزراعي',
                'slug' => 'agricultural-marketing-export',
                'type' => 'feasibility_study',
            ],
            [
                'name' => 'دراسات جدوى مشاريع الاقتصاد الدائري وإعادة تدوير المخلفات وصناعة الأسمدة والمبيدات العضوية',
                'slug' => 'circular-economy-and-organic-inputs',
                'type' => 'feasibility_study',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}