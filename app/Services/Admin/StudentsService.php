<?php

namespace App\Services\admin;

use App\Models\Student;
use App\Repositories\Admin\StudentRepository;

class StudentsService
{
    public function __construct(protected StudentRepository $studentRepository)
    {
        // 
    }
    public function getStudents(array $filters)
    {
        $allowedPerPage = [1, 5, 10, 25, 50, 100];

        $perPage = $filters['per_page'] ?? 10;
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        return $this->studentRepository->search($filters, $perPage);
    }

    public function createStudent($data)
    {
        return $this->studentRepository->create($data);
    }

    public function updateStudent($data, int $id)
    {
        return $this->studentRepository->update($data, $id);
    }
}
