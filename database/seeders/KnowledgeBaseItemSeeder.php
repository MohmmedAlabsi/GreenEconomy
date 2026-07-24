<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KnowledgeBaseItem;

class KnowledgeBaseItemSeeder extends Seeder
{
    public function run(): void
    {
        KnowledgeBaseItem::factory(20)->create();
    }
}