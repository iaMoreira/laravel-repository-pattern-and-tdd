<?php

namespace App\Http\Controllers;

use App\Repositories\Panel\BaseRepository;
use App\Traits\ResponseTrait;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as ParentController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
abstract class BaseController extends ParentController
{
    use ResponseTrait, AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    protected $repository;

    /**
     * Instance that extends Illuminate\Http\Resources\Json\JsonResource
     *
     * @var Illuminate\Http\Resources\Json\JsonResource
     */
    protected $resource;

    public function index(Request $request)
    {
        $data = $request->all();
        $itens = $this->repository->findBy($data);
        return $this->respondWithCollection($itens, $this->resource);
    }

    public function show($id)
    {
        $item = $this->repository->findOne($id);

        if ($item) {
            return $this->respondWithObject($item, $this->resource);
        } else {
            return $this->responseNotFound(['id' => $id]);
        }
    }

    public function store(Request $request)
    {
        // Validation
        $validatorResponse = $this->validateRequest($request);

        // Send failed response if empty request
        if (empty($request->all())) {
            return $this->responseEmpty();
        }

        // Send failed response if validation fails and return array of errors
        if (!empty($validatorResponse)) {
            return $this->responseValidation($validatorResponse);
        }
        $data = $request->all();

        //Begin Database Operations
        DB::beginTransaction();
        try {
            $model = $this->repository->store($data);
            DB::commit();
            return $this->setStatusCode(201)->respondWithObject($model, $this->resource);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseErrorException($e);
        }
    }

    public function update(Request $request, $id)
    {
        // Validation
        $validatorResponse = $this->validateRequest($request, $id);
        $data = $request->all();

        // Send failed response if empty request
        if (empty($data)) {
            return $this->responseEmpty();
        }

        // Send failed response if validation fails and return array of errors
        if (!empty($validatorResponse)) {
            return $this->responseValidation($validatorResponse);
        }

        DB::beginTransaction();
        try {
            $model = $this->repository->update($id, $data);
            DB::commit();
            return $this->respondWithObject($model, $this->resource);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseErrorException($e);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->repository->delete($id);
            DB::commit();
            return $this->responseDeleted();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseErrorException($e);
        }
    }

    protected function validateRequest(Request $request, int $id = null)
    {
        //Perform Validation
        $validator = Validator::make(
            $request->all(),
            $this->repository->getRules($id)
        );
        return $this->getValidationErrors($validator);
    }

    public function getValidationErrors($validator)
    {
        return $validator->errors()->getMessages();
        // $result = [];
        // if ($validator->fails()) {
        //     $errorTypes = $validator->failed();
        //     $messages = $validator->errors()->getMessages();
        //     // crete error message by using key and value
        //     foreach ($errorTypes as $key => $value) {
        //         $result[$key] = $value;
        //     }
        // }

        // return $result;
    }
}
