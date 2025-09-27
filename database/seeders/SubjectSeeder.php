<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\SubjectTranslation;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            [
                'translations' => [
                    ['language_id' => 1, 'name' => 'Анатомия'],
                    ['language_id' => 2, 'name' => 'Анатомия'],
                    ['language_id' => 3, 'name' => 'Анатомия'],
                ],
            ],
            [
                'translations' => [
                    ['language_id' => 1, 'name' => 'Физиология'],
                    ['language_id' => 2, 'name' => 'Физиология'],
                    ['language_id' => 3, 'name' => 'Физиология'],
                ],
            ],
            [
                'translations' => [
                    ['language_id' => 1, 'name' => 'Биохимия'],
                    ['language_id' => 2, 'name' => 'Биокимё'],
                    ['language_id' => 3, 'name' => 'Биохимия'],
                ],
            ],
        ];

        foreach ($subjects as $subjectData) {
            $subject = Subject::create();

            foreach ($subjectData['translations'] as $translation) {
                SubjectTranslation::create([
                    'subject_id'  => $subject->id,
                    'language_id' => $translation['language_id'],
                    'name'        => $translation['name'],
                ]);
            }
        }
    }
}
