<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SkillCategory;
use App\Models\Question;

class SelfHelpSkillsSeeder extends Seeder
{
    public function run()
    {
        $category = SkillCategory::where('slug', 'self-help')->firstOrFail();

        $questions = [
            2 => [
                'open door using knob',
                'suck through a straw',
                'take off clothes without buttons',
                'pull off pants',
            ],
            3 => [
                'eat independently',
                'pour liquid from one container to another',
                'put on shoes without laces',
                'unbutton clothing',
            ],
            4 => [
                'go to the toilet alone',
                'wipe after bowel movement',
                'wash face and hands',
                'brush teeth alone',
                'button clothing',
                'use a fork well',
            ],
            5 => [
                'spread with a knife',
                'dress independently',
                'bathe independently',
            ],
            6 => [
                'act like the “big kid” who takes care of a younger child',
                'dress self, needing help only with tricky buttons or laces',
            ],
            7 => [
                'prefer a learning style (e.g., hands-on, quiet working)',
                'tie their own shoelaces',
                'transition from bath to shower independently',
            ],
            8 => [
                'show more independence from parents and family',
                'understand their place in the world',
                'prefer certain activities or school subjects',
                'brush teeth without help',
            ],
            9 => [
                'think independently and make better decisions',
                'get dressed, brush hair and teeth, and prepare without help',
            ],
            10 => [
                'listen to parents but may start showing irritation with adults in charge',
            ],
            11 => [
                'become more aware of their body as puberty begins',
                'gain more independence from family',
                'begin developing self-regulation skills',
                'understand that thoughts are private',
                'dedicate more time to hobbies',
            ],
            12 => [
                'question family values while developing personal morals',
                'experience fluctuating self-esteem',
            ],
        ];

        foreach ($questions as $age => $items) {
            foreach ($items as $text) {
                Question::create([
                    'skill_category_id' => $category->id,
                    'age' => $age,
                    'text' => ucfirst($text),
                ]);
            }
        }
    }
}
