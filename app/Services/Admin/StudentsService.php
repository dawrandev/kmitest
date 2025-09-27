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
    public function getAllStudents(array $filters)
    {
        return $this->studentRepository->getFilteredStudents($filters);
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
