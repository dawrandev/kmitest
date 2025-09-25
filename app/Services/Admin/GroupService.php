<?php

namespace App\Services\Admin;

use App\Repositories\Admin\GroupRepository;

class GroupService
{
    public function __construct(protected GroupRepository $groupRepository)
    {
        // 
    }

    public function createGroup($data)
    {
        return $this->groupRepository->create($data);
    }

    public function updateGroup($data, int $id)
    {
        return $this->groupRepository->update($data, $id);
    }

    public function getGroups($facultyId = null, $perPage = 10)
    {
        return $this->groupRepository->getAllWithFaculty($facultyId, $perPage);
    }
}
