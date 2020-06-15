<?php

namespace App\Http\Controllers;

use App\Exceptions\BaseException;
use App\Repository\BaseRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

abstract class BaseController extends Controller
{
    protected $repository;

    public function __construct(BaseRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $filters = (array) $request->all();

        $models = $this->repository->findAll($filters);
        return $this->successResponse($models);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        DB::beginTransaction();
        try {
            $model = $this->repository->store($data);
            DB::commit();
            return $this->successResponse($model, 201);
        } catch (Exception $ex){
            DB::rollBack();
            return $this->erroResponse($ex);
        }
    }

    public function show($id)
    {
        $model = $this->repository->findOne($id);
        return $this->successResponse($model);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        // valida requisição

        DB::beginTransaction();
        try {
            $updatedModel = $this->repository->update($id, $data);
            return $this->successResponse($updatedModel);
        } catch (Exception $ex){
            DB::rollBack();
            return $this->erroResponse($ex);

        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->repository->delete($id);
            DB::commit();
            return $this->successResponse(null, 204);
        } catch (Exception $ex){
            DB::rollBack();
            return $this->erroResponse($ex);
        }
    }


    private function successResponse($data, $code = 200)
    {
        return response()->json([
            'status'        => true,
            'code'          => $code,
            'data'          => $data,
        ], $code);
    }

    private function erroResponse($message = 'API.internal_error', $code = 500)
    {
        return response()->json([
            'status'        => false,
            'code'          => 500,
            'message'       => $message
        ], $code);
    }

    protected function errorExceptionResponse(Exception $ex)
    {
        $message = 'API.unable_complete_operation';
        $code = 500;
        if($ex instanceof BaseException) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
        }
        return response()->json([
            'status'        => false,
            'code'          => $code,
            'message'       => $message
        ]);
    }
}
