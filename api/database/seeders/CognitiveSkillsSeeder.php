<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SkillCategory;
use App\Models\Question;

class CognitiveSkillsSeeder extends Seeder
{
    public function run()
    {
        $category = SkillCategory::where('slug', 'cognitive')->firstOrFail();

        $questions = [
            2 => [
                'sort objects',
                'match objects to pictures',
                'show use of familiar objects',
            ],
            3 => [
                'draw a two-to-three-part person',
                'understand long/short/big/small and more/less',
                'know own gender',
                'know own age',
                'match letters or numerals',
            ],
            4 => [
                'draw a four-to-six-part person',
                'give amounts under 5 correctly',
                'complete simple analogies like dad/boy or mother/??',
                'complete simple analogies like ice/cold or fire/??',
                'complete simple analogies like ceiling/up or floor/??',
                'point to 5 to 6 colors',
                'point to letters or numerals when named',
                ' count to 4',
                'recognize several common signs or store names',
            ],
            5 => [
                'draw an eight-to-ten-part person',
                'give amounts up to 19',
                'identify coins',
                'name letters or numerals out of order',
                ' count to 10',
                'name 10 colors',
                'use letter names as sounds to invent spelling',
                'know sounds of consonants and short vowels',
                'read 25 words',
            ],
            6 => [
                'tell their age',
                'count to 10 and understand the concept of 10',
                'express themselves clearly with words',
                'understand cause-and-effect relationships',
                'write basic words or sentences',
                'grasp the concept of time',
            ],
            7 => [
                'understand seconds, minutes, hours, days, weeks, and months',
                'solve simple math problems using objects',
                'consider one factor at a time in problem-solving',
                'describe similarities between two objects',
                'begin understanding that letters represent sounds',
                'solve more complex problems',
                'understand the difference between right and wrong',
            ],
            8 => [
                'think about the future',
                'try to understand why things happen',
                'think in an organized and logical way',
                'understand reversibility (e.g., 2+4=6 and 6-4=2)',
            ],
            9 => [
                'identify object uses and group them by category',
                'add and subtract two-digit numbers',
                'understand fractions',
            ],
            10 => [
                'state the complete date',
                'name months of the year in order',
                'perform skilled addition and subtraction',
            ],
            11 => [
                'focus for longer periods of time',
                'solve more complicated problems',
                'realize that current choices have long-term consequences',
                'face academic challenges in school',
            ],
            12 => [
                'use logic to think through problems',
                'define self-concept based on school success',
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
