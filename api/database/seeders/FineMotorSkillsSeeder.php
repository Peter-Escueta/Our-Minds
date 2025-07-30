<?php

namespace Database\Seeders;

use App\Models\DevelopmentalChecklist;
use App\Models\Question;
use Illuminate\Database\Seeder;
use App\Models\SkillCategory;

class FineMotorSkillsSeeder extends Seeder
{
    public function run(): void
    {
        $category = SkillCategory::where('slug', 'fine-motor')->firstOrFail();

        $data = [
            2 => [
                'Make a single-line “train” using cubes',
                'Imitate a circle',
                'Imitate a horizontal line',
            ],
            3 => [
                'Copy a circle',
                'Cut side-to-side with scissors (awkwardly)',
                'String small beads well',
                'Imitate a bridge using cubes',
            ],
            4 => [
                'Copy a square',
                'Tie a single knot',
                'Cut out a 5-inch circle',
                'Use tongs to transfer objects',
                'Write part of their first name',
                'Imitate a gate using cubes',
            ],
            5 => [
                'Copy a triangle',
                'Put a paperclip on paper',
                'Use clothespins to transfer small objects',
                'Cut effectively with scissors',
                'Write their first name',
                'Build stairs from a model',
            ],
            6 => [
                'Learn to write',
                'Draw a person with at least 8 parts',
                'Use safety scissors easily',
                'Write their full name',
                'Write some letters and numbers',
            ],
            7 => [
                'Use a pencil to write their name',
                'Throw a ball accurately at a target',
                'Catch a ball with one hand',
            ],
            8 => [
                'Cut out irregular shapes',
                'Write smaller letters within the lines of school books',
                'Use fingers with refined control',
            ],
            9 => [
                'Draw, paint, or make jewelry as a hobby',
                'Use simple tools like a hammer independently',
            ],
            10 => [
                'Write with clearer handwriting due to stronger fine motor skills',
            ],
            11 => [
                'Write with improved handwriting',
                'Use a variety of tools effectively',
            ],
            12 => [
                'Enjoy drawing, writing, and painting',
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
