<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use App\Models\BaseModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BaseRepository
{
    /**
     * Find a resource by id
     *
     * @param $id
     * @return BaseModel|null
     */
    public function findOne(int $id): ?BaseModel;

    /**
     * Find a resource by id
     *
     * @param $id
     * @return BaseModel
     * @throws BaseException
     */
    public function findOneOrFail(int $id): BaseModel;
    
    /**
     * Find a resource by criteria
     *
     * @param array $criteria
     * @return BaseModel|null
     */
    public function findOneBy(array $criteria): ?BaseModel;

    /**
     * Search All resources by criteria
     *
     * @param array $searchCriteria
     * @return Collection
     */
    public function findBy(array $searchCriteria = []): LengthAwarePaginator;

    /**
     * Search All resources by any values of a key
     *
     * @param string $key
     * @param array $values
     * @return Collection
     */
    public function findIn(string $key, array $values): Collection;

    /**
     * Save a resource
     *
     * @param array $data
     * @return BaseModel
     */
    public function store(array $data): BaseModel;

    /**
     * Update a resource
     *
     * @param integer $id
     * @param array $data
     * @return BaseModel
     * @throws BaseException
     */
    public function update(int $id, array $data): BaseModel;

    /**
     * Delete a resource
     *
     * @param integer $id
     * @return bool
     * @throws BaseException
     */
    public function delete(int $id): bool;
}