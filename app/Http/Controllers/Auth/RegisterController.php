<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterFormRequest;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\RegisterRepositoryInterface;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    use ResponseTrait;


    protected $repository;
    protected $resource;

    public function __construct(RegisterRepositoryInterface $repository, UserResource $resource)
    {
        $this->repository = $repository;
        $this->resource = $resource;
    }

    public function create(RegisterFormRequest $request)
    {
        $data = $request->all();

        DB::beginTransaction();
        try {
            $user = $this->repository->create($data);
            DB::commit();
            return $this->setStatusCode(201)->setMessage("API.welcome_user_send_confirmation_email")->respondWithObject($user, $this->resource);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseErrorException($e);
        }
    }

    public function confirmAccount($token)
    {
        DB::beginTransaction();
        try {
            $user = $this->repository->confirmAccount($token);
            DB::commit();
            return $this->setMessage('API.successfully_activated')->respondWithObject($user, $this->resource);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseErrorException($e);
        }
    }
}
