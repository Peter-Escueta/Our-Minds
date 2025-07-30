<?php

namespace Database\Seeders;

use App\Models\SkillCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  public function run(): void
    {
        $this->call([
            SkillCategorySeeder::class,
            UserSeeder::class,
            LanguageSkillQuestionsSeeder::class,
            SelfHelpSkillsSeeder::class,
            CognitiveSkillsSeeder::class,
            GrossMotorSkillsSeeder::class,
            PsychosocialSkillsSeeder::class,
            FineMotorSkillsSeeder::class,
        ]);
    }
}