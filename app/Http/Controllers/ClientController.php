<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use App\Repositories\Contracts\ClientRepositoryInterface;

class ClientController extends BaseController
{
    public function __construct(ClientRepositoryInterface $repository, ClientResource $reosurce)
    {
        $this->repository = $repository;
        $this->resource = $reosurce;
    }
}
