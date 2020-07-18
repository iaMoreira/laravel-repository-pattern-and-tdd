<?php

namespace App\Repositories\Contracts;

interface LoginRepositoryInterface
{
    public function login(array $credencials): array;

    public function refreshToken(): array;

    public function logout(): void;
}
