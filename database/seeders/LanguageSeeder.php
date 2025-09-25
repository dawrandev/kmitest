<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            [
                'name' => 'Каракалпак',
                'code' => 'kk'
            ],
            [
                'name' => 'Узбек',
                'code' => 'uz'
            ],
            [
                'name' => 'Русский',
                'code' => 'ru'
            ]
        ];
        foreach ($languages as $language) {
            Language::create($language);
        }
    }
}
