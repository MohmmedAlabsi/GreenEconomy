<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::insert([
            [
                'name' => 'الصناعات الغذائية',
                'slug' => 'food-industries',
                'type' => 'sector',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'الصناعات الدوائية',
                'slug' => 'pharmaceutical-industries',
                'type' => 'sector',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'الصناعات الكيميائية',
                'slug' => 'chemical-industries',
                'type' => 'sector',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'الصناعات البلاستيكية',
                'slug' => 'plastic-industries',
                'type' => 'sector',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'الصناعات المعدنية',
                'slug' => 'metal-industries',
                'type' => 'sector',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'الصناعات الهندسية',
                'slug' => 'engineering-industries',
                'type' => 'sector',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'الصناعات الكهربائية والإلكترونية',
                'slug' => 'electrical-electronics',
                'type' => 'sector',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'مواد البناء',
                'slug' => 'building-materials',
                'type' => 'sector',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'إعادة التدوير والاقتصاد الأخضر',
                'slug' => 'recycling-green-economy',
                'type' => 'sector',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'الطاقة المتجددة',
                'slug' => 'renewable-energy',
                'type' => 'sector',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}