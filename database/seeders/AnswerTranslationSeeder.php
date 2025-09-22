<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnswerTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $answers = [
            [
                'answer_id' => 1,
                'language_id' => 1,
                'text' => 'Qızılǵa'
            ],
            [
                'answer_id' => 1,
                'language_id' => 2,
                'text' => 'Қызылға'
            ],
            [
                'answer_id' => 1,
                'language_id' => 3,
                'text' => 'Qizilga'
            ],
            [
                'answer_id' => 1,
                'language_id' => 4,
                'text' => 'Қизилга'
            ],
            [
                'answer_id' => 1,
                'language_id' => 5,
                'text' => 'Красный'
            ],
        ];
    }
}
