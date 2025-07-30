<?php

namespace Database\Seeders;

use App\Models\DevelopmentalChecklist;
use App\Models\Question;
use App\Models\SkillCategory;
use Illuminate\Database\Seeder;

class PsychosocialSkillsSeeder extends Seeder
{
    public function run(): void
    {
        $category = SkillCategory::where('slug', 'psychosocial')->firstOrFail();

        $data = [
            2 => [
                'Play in parallel with other children',
                'Mask emotions to follow social etiquette',
            ],
            3 => [
                'Share with or without being prompted',
                'Express fear of imaginary things',
                'Engage in imaginative play',
                'Describe what someone else is thinking',
            ],
            4 => [
                'Use deception or tricks in play',
                'Have a preferred friend',
                'Label emotions like happiness, sadness, fear, and anger',
                'Engage in group play',
            ],
            5 => [
                'Have a group of friends',
                'Apologize for mistakes',
                'Respond verbally to others’ good fortune',
            ],
            6 => [
                'Understand others’ feelings with encouragement',
                'Feel jealousy toward siblings or friends',
                'Struggle with frustration in games or skills',
                'Seek interaction with parents but fulfill needs with others',
                'Play with rich imagination',
                'Understand cause-and-effect relationships',
                'Focus on a task for at least 15 minutes',
                'Develop a sense of humor (like simple jokes or rhymes)',
            ],
            7 => [
                'Be aware of and sensitive to others’ emotions',
                'Overcome some childhood fears',
                'Read emotions from facial expressions',
                'Show strong emotional reactions',
                'Feel guilt and shame',
                'Desire to be perfect or self-critical',
                'Form friendships (often same gender)',
                'Play in large groups sometimes',
                'Withdraw from or avoid adults',
                'Wait their turn during activities',
            ],
            8 => [
                'Talk about their feelings',
                'Need love and understanding',
                'Display quick emotional changes',
                'Be impatient',
                'Value friendships and teamwork',
                'Want to be liked and accepted by friends',
                'Show concern for others',
                'Be helpful, pleasant, or cheerful, but also bossy or rude',
                'Make friends easily',
                'Be overdramatic',
            ],
            9 => [
                'Understand appropriate behavior',
                'Control anger most of the time',
                'Show empathy',
                'Experience fewer mood swings',
                'Make plans with friends ahead of time',
            ],
            10 => [
                'Control emotions better',
                'Quarrel frequently with siblings',
                'Experience more stress',
                'Enjoy time with friends',
                'Have a best friend (often same gender)',
            ],
            11 => [
                'Feel peer pressure more strongly',
                'Understand others’ perspectives more clearly',
                'Value friendships emotionally',
                'Resist physical affection from parents',
                'Form strong and complex friendships',
                'Explore identity (e.g., clothes, hobbies, peers)',
            ],
            12 => [
                'Develop and test values and beliefs',
                'Experience frequent emotional highs and lows',
                'Show rebellious behavior',
                'Seek peer approval',
                'Develop a crush or romantic interest',
            ],
        ];

        foreach ($data as $age => $questions) {
            foreach ($questions as $question) {
                Question::create([
                 'skill_category_id' => $category->id,
                    'age' => $age,
                    'text' => ucfirst($question),
                ]);
            }
        }
    }
}
