<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Topic;
use App\Models\TopicTranslation;

class TopicSeeder extends Seeder
{
    public function run(): void
    {
        $topics = [
            [
                'subject_id' => 1,
                'translations' => [
                    ['language_id' => 1, 'name' => 'Скелет системасы'],
                    ['language_id' => 2, 'name' => 'Скелет системаси'],
                    ['language_id' => 3, 'name' => 'Скелетная система'],
                ],
            ],
            [
                'subject_id' => 2,
                'translations' => [
                    ['language_id' => 1, 'name' => 'Кан айланыў системасы'],
                    ['language_id' => 2, 'name' => 'Қон айланиш тизими'],
                    ['language_id' => 3, 'name' => 'Кровеносная система'],
                ],
            ],
            [
                'subject_id' => 3,
                'translations' => [
                    ['language_id' => 1, 'name' => 'Белоклардың дүзилиси ҳәм функциялары.'],
                    ['language_id' => 2, 'name' => 'Оқсилларнинг тузилиши ва вазифалари'],
                    ['language_id' => 3, 'name' => 'Структура и функции белков'],
                ],
            ],
        ];

        foreach ($topics as $topicData) {
            $topic = Topic::create([
                'subject_id' => $topicData['subject_id'],
            ]);

            foreach ($topicData['translations'] as $translation) {
                TopicTranslation::create([
                    'topic_id'    => $topic->id,
                    'language_id' => $translation['language_id'],
                    'name'        => $translation['name'],
                ]);
            }
        }
    }
}
