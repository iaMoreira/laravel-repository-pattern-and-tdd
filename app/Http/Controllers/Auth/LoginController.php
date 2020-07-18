<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginFormRequest;
use App\Repositories\Contracts\LoginRepositoryInterface;
use App\Traits\ResponseTrait;

class LoginController extends Controller
{
    use ResponseTrait;

    /**
     * @var LoginRepositoryInterface
     */
    private $repository;

    public function __construct(LoginRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function login(LoginFormRequest $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            $data = $this->repository->login($credentials);
            return $this->responseWithToken($data);
        } catch (\Exception $e) {
            return $this->responseErrorException($e);
        }
    }

    public function refreshToken()
    {
        try {
            $data = $this->repository->refreshToken();
            return $this->responseWithToken($data);
        } catch (\Exception $ex) {
            return $this->responseErrorException($ex);
        }
    }

    public function logout()
    {
        try {
            $this->repository->logout();
            return $this->setMessage('API.logout')->setStatusCode(204)->respond();
        } catch (\Exception $ex) {
            return $this->responseErrorException($ex);
        }
    }
}
