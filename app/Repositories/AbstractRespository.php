<?php

namespace App\Repositories;

use App\Exceptions\BaseException;
use App\Models\BaseModel;
use App\Models\User;
use App\Repositories\Contracts\BaseRepository;
use App\Traits\FiltersModelTrait;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

use Tymon\JWTAuth\Facades\JWTAuth;

abstract class AbstractRepository implements BaseRepository
{
    use FiltersModelTrait;


    /**
     * Instance that 
     *
     * @var BaseModel $model
     */
    protected $model;

    /**
     * get logged in user
     *
     * @var User $currentUser
     */
    protected $currentUser;

    public function __construct(BaseModel $model)
    {
        $this->model = $model;
        // $this->currentUser = $this->getCurrentUser();
    }

    /**
     * Get BaseModel instance
     *
     * @return BaseModel
     */
    public function getModel(): BaseModel
    {
        return $this->model;
    }

    /**
     * Find a resource by id
     *
     * @param $id
     * @return BaseModel|null
     */
    public function findOne(int $id): ?BaseModel
    {
        return $this->model->where('id', $id)->first();
    }

    /**
     * Find a resource by id or fail
     *
     * @param $id
     * @return BaseModel
     * @throws BaseException
     */
    public function findOneOrFail(int $id): BaseModel
    {
        $model = $this->model->where('id', $id)->first();
        if (\is_null($model)) {
            throw new BaseException('API.' . $this->getClassName() . '_not_found', 404);
        }

        return $model;
    }

    /**
     * Find a resource by criteria
     *
     * @param array $criteria
     * @return BaseModel|null
     */
    public function findOneBy(array $criteria): ?BaseModel
    {
        return $this->model
            ->where($criteria)
            ->first();
    }

    /**
     * Search All resources by any values of a key
     *
     * @param string $key
     * @param array $values
     * @return Collection
     */
    public function findIn(string $key, array $values): Collection
    {
        return $this->model->whereIn($key, $values)->get();
    }

    /**
     * Search All resources by criteria
     *
     * @param array $searchCriteria
     * @return Collection
     */
    public function findBy(array $searchCriteria = []): LengthAwarePaginator
    {
        $limit = !empty($searchCriteria['per_page']) ? (int) $searchCriteria['per_page'] : 15; // it's needed for pagination

        $queryBuilder = $this->model->where(function ($query) use ($searchCriteria) {
            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
        });

        if (isset($searchCriteria['order_by'])) {
            $queryBuilder->orderBy($searchCriteria['order_by']['field'], $searchCriteria['order_by']['direction']);
        }

        $this->applyFilterByIntervalDates($queryBuilder, $searchCriteria);

        return $queryBuilder->paginate($limit);
    }

    /**
     * Save a resource
     *
     * @param array $data
     * @return BaseModel
     */
    public function store(array $data): BaseModel
    {
        $filledProperties = $this->model->getFillable();
        $keys = array_keys($data);

        foreach ($keys as $key) {
            // update only fillAble properties
            if (!in_array($key, $filledProperties)) {
                unset($data[$key]);
            }
        }

        $model = $this->model->create($data);
        return $model;
    }

    /**
     * Update a resource
     *
     * @param integer $id
     * @param array $data
     * @return BaseModel
     * @throws BaseException
     */
    public function update(int $id, array $data): BaseModel
    {
        $model = $this->findOneOrFail($id);

        $filledProperties = $this->model->getFillable();
        $keys = array_keys($data);
        foreach ($keys as $key) {
            // update only fillAble properties
            if (in_array($key, $filledProperties)) {
                $model->$key = $data[$key];
            }
        }

        $model->save();
        return $model;
    }

    /**
     * Delete a resource
     *
     * @param integer $id
     * @return bool
     * @throws BaseException
     */
    public function delete(int $id): bool
    {
        $model = $this->findOneOrFail($id);
        return $model->delete();
    }

    /**
     * get loggedIn user
     *
     * @return User
     * @throws BaseException
     */
    public function getCurrentUser(): User
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            throw new BaseException("API.user_not_found");
        }
        return $user;
    }

    /**
     * get class name of resource
     *
     * @return string
     */
    protected function getClassName(): string
    {
        $array = explode('\\', get_class($this->model));
        return strtolower(end($array));
    }

    /**
     * get rules of resource
     *
     * @return string
     */
    public function getRules(int $item = null): array
    {
        return $this->model::getRules($item);
    }
}
