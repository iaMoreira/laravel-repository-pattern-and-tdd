<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\AuthRepositoryInterface;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ResponseTrait;

    /**
     * @var AuthRepositoryInterface
     */
    private $repository;

    public function __construct(AuthRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function login(Request $request)
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
