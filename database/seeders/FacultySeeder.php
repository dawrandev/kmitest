<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\FacultyTranslation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FacultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faculties = [
            [
                'faculty_id' => 1,
                'language_id' => 1,
                'name' => 'Педиатрия',
            ],
            [
                'faculty_id' => 1,
                'language_id' => 2,
                'name' => 'Педиатрия',
            ],
            [
                'faculty_id' => 1,
                'language_id' => 3,
                'name' => 'Педиатрия',
            ],
        ];
        Faculty::create();
        foreach ($faculties as $faculty) {
            FacultyTranslation::create($faculty);
        }
    }
}
