<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SkillCategory;
use App\Models\Question;

class GrossMotorSkillsSeeder extends Seeder
{
    public function run()
    {
        $category = SkillCategory::where('slug', 'gross-motor')->firstOrFail();

        $questions = [
            2 => [
                'walk and run fairly well',
                'walk down stairs holding rail, placing both feet on each step',
                'jump in place with both feet off the ground',
                'walk up and down stairs alone',
                'kick a ball with either foot',
            ],
            3 => [
                'go upstairs alternating feet without using a rail',
                'balance on one foot for a few seconds',
                'jump forward 10 to 24 inches',
                'catch a large ball',
                'pedal a tricycle',
            ],
            4 => [
                'run, jump, and climb well',
                'hop proficiently on one foot',
                'do a hopscotch',
                'catch a ball reliably',
                'begin doing somersaults',
            ],
            5 => [
                'skip on alternate feet and jump rope',
                'begin to skate and swim',
                'ride a bicycle with or without training wheels',
                'climb well',
            ],
            6 => [
                'run, skip, and jump regularly',
                'catch a ball',
                'skip with ease',
                'control major muscles well',
                'maintain good balance',
            ],
            7 => [
                'show coordination in climbing or swimming',
                'perform simple gymnastics movements like somersaults',
                'ride a two-wheeled bike',
                'maintain good balance',
            ],
            8 => [
                'perform complex movements like zig-zag running and cartwheels',
                'run to kick a ball effectively',
                'run further due to increased stamina',
            ],
            9 => [
                'engage in active play like swimming, biking, or running games',
                'show interest in team sports',
                'demonstrate improved coordination',
            ],
            10 => [
                'enjoy team and group activities',
                'have built endurance for physical activities like running',
                'enjoy activities that use large and small muscles such as dancing or sports',
            ],
            11 => [
                'experience increased need for sleep and food due to growth',
            ],
            12 => [
                'show increased muscular development (boys)',
                'experience early puberty changes (girls)',
                'have increased coordination',
                'show improved skill in sports',
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
