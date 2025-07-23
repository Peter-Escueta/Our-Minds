<?php

namespace Database\Seeders;

use App\Models\SkillCategory;
use Illuminate\Database\Seeder;

class SkillCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Psychosocial Skills', 'slug' => 'psychosocial', 'color' => 'bg-red-700'],
            ['name' => 'Language Skills', 'slug' => 'language', 'color' => 'bg-blue-700'],
            ['name' => 'Fine Motor Skills', 'slug' => 'fine-motor', 'color' => 'bg-green-700'],
            ['name' => 'Cognitive Skills', 'slug' => 'cognitive', 'color' => 'bg-yellow-700'],
            ['name' => 'Gross Motor Skills', 'slug' => 'gross-motor', 'color' => 'bg-purple-700'],
        ];

        foreach ($categories as $category) {
            SkillCategory::create($category);
        }
    }
}