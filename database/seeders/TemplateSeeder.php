<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'number' => 1,
            ],
            [
                'number' => 2,
            ],
        ];
        foreach ($templates as $template) {
            \App\Models\Template::create($template);
        }
    }
}
