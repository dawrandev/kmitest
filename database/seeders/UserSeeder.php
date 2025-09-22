<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'login' => 'admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin'
            ],
            [
                'login' => 'student',
                'password' => Hash::make('student123'),
                'role' => 'student'
            ]
        ];

        foreach ($users as $user) {
            \App\Models\User::create($user);
        }
    }
}
