<?php

namespace App\Repositories;

use App\Exceptions\BaseException;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    private $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Find a user by id or fail
     *
     * @param $id
     * @return User
     * @throws BaseException
     */
    public function findOneOrFail(int $id): User
    {
        $model = $this->model->where('id', $id)->first();
        if (\is_null($model)) {
            throw new BaseException('API.user_not_found', 404);
        }

        return $model;
    }

    /**
     * Save a user
     *
     * @param array $data
     * @return User
     */
    public function store(array $data): User
    {
        $filledProperties = $this->model->getFillable();
        $keys = array_keys($data);
        $data['password'] = bcrypt($data['password']);

        foreach ($keys as $key) {
            if (!in_array($key, $filledProperties)) {
                unset($data[$key]);
            }
        }

        $model = $this->model->create($data);
        return $model;
    }

    /**
     * Update a user
     *
     * @param integer $id
     * @param array $data
     * @return User
     * @throws BaseException
     */
    public function update(int $id, array $data): User
    {
        $model = $this->findOneOrFail($id);

        $filledProperties = $this->model->getFillable();
        $keys = array_keys($data);
        foreach ($keys as $key) {
            if (in_array($key, $filledProperties)) {
                $model->$key = $data[$key];
            }
        }

        $model->save();
        return $model;
    }

    /**
     * Find a resource by criteria
     *
     * @param array $criteria
     * @return User|null
     */
    public function findOneBy(array $criteria): ?User
    {
        return $this->model->where($criteria)->first();
    }
}