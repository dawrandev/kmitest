<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = [
            [
                'user_id' => 2,
                'full_name' => 'Dawranbek Sipatdinov',
                'phone' => '933651302',
                'address' => 'Nokis',
            ],
        ];

        foreach ($students as $student) {
            \App\Models\Student::create($student);
        }
    }
}
