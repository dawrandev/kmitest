<?php

namespace App\Repositories\Admin;

use App\Models\Student;
use App\Models\User;

class StudentRepository
{
    public function search(array $filters, int $perPage = 10)
    {
        $query = Student::query();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%")
                    ->orWhere('address', 'LIKE', "%{$search}%");
            });
        }

        return $query->paginate($perPage)->appends($filters);
    }

    public function create($data)
    {
        $user = User::create([
            'role' => 'student',
            'login' => $data['login'],
            'password' => $data['password'],
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'full_name' => $data['full_name'],
            'phone' => $data['phone'],
            'address' => $data['address'],
        ]);

        return $student;
    }

    public function update($data, int $id)
    {
        $user = User::where('id', $id)->update([
            'login' => $data['login'],
            'password' => $data['password'],
        ]);

        $student = Student::where('user_id', $id)->update([
            'full_name' => $data['full_name'],
            'phone' => $data['phone'],
            'address' => $data['address'],
        ]);

        return $student;
    }
}
