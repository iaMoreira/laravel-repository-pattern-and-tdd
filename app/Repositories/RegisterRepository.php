<?php

namespace App\Repositories;

use App\Exceptions\BaseException;
use App\Models\User;
use App\Repositories\Contracts\RegisterRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Str;

class RegisterRepository implements RegisterRepositoryInterface
{

    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function create(array $data): User
    {
        $data['status'] = 'new';
        $user = $this->userRepository->store($data);
        $user->remember_token = Str::random(40);
        $user->save();
        // config('app.debug') ?? SendWelcomeEmail::dispatch($user);
        return $user;
    }

    public function confirmAccount(string $token): User
    {
        $user = $this->getUserByTokenActivation($token);
        if ($user->status == 'active') {
            throw new  BaseException('API.user_already_active', 400);
        }

        $data['status'] = 'active';
        $data['email_verified_at'] = date("Y-m-d H:i:s");
        $user = $this->userRepository->update($user->id, $data);
        return $user;
    }

    public function getUserByTokenActivation(string $token): User
    {
        $user = $this->userRepository->findOneBy(['remember_token' => $token]);
        if (!$user) {
            throw new BaseException('API.token_not_found', 404);
        }
        return $user;
    }
}
