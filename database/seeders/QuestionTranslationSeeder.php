<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions =
            [
                [
                    'question_id' => 1,
                    'language_id' => 1,
                    'text' => 'Bul belgilerdiń təsir etiw aymaǵında qaysı avtomobilge toqtawǵa ruxsat etiledi?',
                    'image' => 'qizil.jpg',
                ],
                [
                    'question_id' => 1,
                    'language_id' => 2,
                    'text' => 'Бул белгилердиӊ тəсир етиў аймағында қайсы автомобильге тоқтаўға рухсат етиледи?',
                    'image' => 'qizil.jpg',
                ],
                [
                    'question_id' => 1,
                    'language_id' => 3,
                    'text' => 'Bu belgilarning ta’sir hududida qaysi avtomobilga to‘xtashga ruxsat etiladi?',
                    'image' => 'qizil.jpg',
                ],
                [
                    'question_id' => 1,
                    'language_id' => 4,
                    'text' => 'Бу белгиларнинг таъсир ҳудудида қайси автомобилга тўхташга рухсат этилади?',
                    'image' => 'qizil.jpg',
                ],
                [
                    'question_id' => 1,
                    'language_id' => 5,
                    'text' => 'Какой автомобиль разрешено остановиться в зоне действия этих знаков?',
                    'image' => 'qizil.jpg',
                ],
            ];

        foreach ($questions as $question) {
            \App\Models\QuestionTranslation::create($question);
        }
    }
}
