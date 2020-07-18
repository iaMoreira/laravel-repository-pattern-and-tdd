<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findOneOrFail(int $id): User;

    public function store(array $data): User;

    public function update(int $id, array $data): User;

    public function findOneBy(array $criteria): ?User;

}