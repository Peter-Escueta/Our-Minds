<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\SkillCategory;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    public function run()
    {
        $categories = SkillCategory::all();

        $questions = [
            'psychosocial' => [
                ['Plays cooperatively with other children', 3],
                ['Shows empathy for others', 4],
                ['Takes turns in games', 5],
                ['Expresses emotions appropriately', 3],
                ['Resolves conflicts with peers', 5],
            ],
            'language' => [
                ['Uses 3-4 word sentences', 3],
                ['Tells simple stories', 4],
                ['Uses future tense correctly', 5],
                ['Follows 3-step instructions', 4],
                ['Understands opposites', 5],
            ],
            'fine-motor' => [
                ['Holds crayon with fingers', 3],
                ['Cuts along a line with scissors', 4],
                ['Writes some letters', 5],
                ['Buttons and unbuttons clothing', 5],
            ],
            'cognitive' => [
                ['Matches colors and shapes', 3],
                ['Counts to 10', 4],
                ['Recognizes some letters', 5],
                ['Understands concept of time', 5],
            ],
            'gross-motor' => [
                ['Jumps with both feet', 3],
                ['Hops on one foot', 4],
                ['Skips alternating feet', 5],
                ['Catches a ball with hands', 5],
            ],
        ];

        foreach ($categories as $category) {
            if (isset($questions[$category->slug])) {
                foreach ($questions[$category->slug] as $question) {
                    Question::create([
                        'skill_category_id' => $category->id,
                        'text' => $question[0],
                        'age' => $question[1],
                    ]);
                }
            }
        }
    }
}