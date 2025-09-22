<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questionTemplates = [
            [
                'template_id' => 1,
                'question_id' => 1,
            ],
            [
                'template_id' => 1,
                'question_id' => 2,
            ],
            [
                'template_id' => 1,
                'question_id' => 3,
            ],
            [
                'template_id' => 2,
                'question_id' => 4,
            ],
            [
                'template_id' => 2,
                'question_id' => 5,
            ],
            [
                'template_id' => 2,
                'question_id' => 6,
            ],

        ];
        foreach ($questionTemplates as $questionTemplate) {
            DB::table('template_questions')->insert($questionTemplate);
        }
    }
}
