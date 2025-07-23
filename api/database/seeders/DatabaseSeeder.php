<?php

namespace Database\Seeders;

use App\Models\SkillCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            SkillCategorySeeder::class,
            QuestionSeeder::class,
            UserSeeder::class,
        ]);
    }
}