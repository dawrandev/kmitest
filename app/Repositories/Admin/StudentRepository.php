<?php

namespace App\Repositories\Admin;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StudentRepository
{
    public function getFilteredStudents(array $filters)
    {
        $query = Student::with(['group.faculty.translations']);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('full_name', 'like', "%{$filters['search']}%")
                    ->orWhere('phone', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['faculty_id'])) {
            $query->whereHas('group.faculty', function ($q) use ($filters) {
                $q->where('id', $filters['faculty_id']);
            });
        }

        if (!empty($filters['group_id'])) {
            $query->where('group_id', $filters['group_id']);
        }

        $perPage = $filters['per_page'] ?? 10;

        return $query->paginate($perPage)->withQueryString();
    }


    public function create($data)
    {
        $user = User::create([
            'role' => 'student',
            'login' => $data['login'],
            'password' => Hash::make($data['password']),
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'faculty_id' => $data['faculty_id'],
            'group_id' => $data['group_id'],
            'full_name' => $data['full_name'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        return $student;
    }

    public function update($data, int $id)
    {
        $student = Student::findOrFail($id);

        $userUpdateData = [];
        if (!empty($data['login'])) {
            $userUpdateData['login'] = $data['login'];
        }
        if (!empty($data['password'])) {
            $userUpdateData['password'] = bcrypt($data['password']);
        }

        if (!empty($userUpdateData)) {
            $student->user->update($userUpdateData);
        }

        $studentUpdateData = [];
        if (!empty($data['full_name'])) {
            $studentUpdateData['full_name'] = $data['full_name'];
        }
        if (!empty($data['phone'])) {
            $studentUpdateData['phone'] = $data['phone'];
        }
        if (!empty($data['group_id'])) {
            $studentUpdateData['group_id'] = $data['group_id'];
        }

        if (!empty($studentUpdateData)) {
            $student->update($studentUpdateData);
        }

        return $student;
    }
}
