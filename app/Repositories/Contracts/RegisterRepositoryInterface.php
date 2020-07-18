<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface RegisterRepositoryInterface
{

    public function create(array $data): User;

    public function confirmAccount(string $token): User;

    public function getUserByTokenActivation(string $token): User;
}
