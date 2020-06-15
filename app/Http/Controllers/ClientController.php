<?php

namespace App\Http\Controllers;

use App\Repository\ClientRepository;

class ClientController extends BaseController
{
    public function __construct(ClientRepository $repository)
    {
        $this->repository = $repository;
    }
}
