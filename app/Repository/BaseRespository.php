<?php

namespace App\Repository;

use App\Exceptions\BaseException;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository {

    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function findOne($id)
    {
        $model = $this->model::where('id', $id)
            ->first();
        if(is_null($model)){
            throw new BaseException('API.'. $this->getClassName() .'_not_found', 404);
        }

        return $model;
    }

    public function findAll(array $filter)
    {
        $items = $this->model::where($filter)
            ->paginate();

        return $items;
    }

    public function store(array $data)
    {
        $filledProperties = $this->model->getFillable();
        $keys = array_keys($data);

        foreach ($keys as $key) {
            // update only fillAble properties
            if (!in_array($key, $filledProperties)) {
                unset($data[$key]);
            }
        }

        $newModel = $this->model->create($data);

        return $newModel;
    }

    public function update($id, array $data)
    {
        $model = $this->findOne($id);

        $filledProperties = $this->model->getFillable();
        $keys = array_keys($data);
        foreach ($keys as $key) {
            // update only fillAble properties
            if (in_array($key, $filledProperties)) {
                $model->$key = $data[$key];
            }
        }
        // update the model
        $model->save();
        return $model;
    }

    public function delete($id)
    {
        $model = $this->findOne($id);
        $model->delete();
    }


    protected function getClassName()
    {
        $array = explode('\\', get_class($this->model));
        return strtolower(end($array));
    }


}
