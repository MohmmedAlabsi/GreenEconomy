<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'الصناعات الغذائية',
                'slug' => 'food-industries',
                'type' => 'sector',
            ],
            [
                'name' => 'الصناعات الدوائية',
                'slug' => 'pharmaceutical-industries',
                'type' => 'sector',
            ],
            [
                'name' => 'الصناعات الكيميائية',
                'slug' => 'chemical-industries',
                'type' => 'sector',
            ],
            [
                'name' => 'الصناعات البلاستيكية',
                'slug' => 'plastic-industries',
                'type' => 'sector',
            ],
            [
                'name' => 'الصناعات المعدنية',
                'slug' => 'metal-industries',
                'type' => 'sector',
            ],
            [
                'name' => 'الصناعات الهندسية',
                'slug' => 'engineering-industries',
                'type' => 'sector',
            ],
            [
                'name' => 'الصناعات الكهربائية والإلكترونية',
                'slug' => 'electrical-electronics',
                'type' => 'sector',
            ],
            [
                'name' => 'مواد البناء',
                'slug' => 'building-materials',
                'type' => 'sector',
            ],
            [
                'name' => 'إعادة التدوير والاقتصاد الأخضر',
                'slug' => 'recycling-green-economy',
                'type' => 'sector',
            ],
            [
                'name' => 'الطاقة المتجددة',
                'slug' => 'renewable-energy',
                'type' => 'sector',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}