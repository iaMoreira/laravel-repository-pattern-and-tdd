<?php

namespace App\Repositories\Contracts;

interface AuthRepositoryInterface
{
    public function login(array $credencials): array;

    public function refreshToken(): array;

    public function logout(): void;
}
