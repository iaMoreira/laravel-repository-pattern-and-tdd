<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductController extends BaseController
{
    public function __construct(ProductRepositoryInterface $repository, ProductResource $resource)
    {
        $this->repository = $repository;
        $this->resource = $resource;
    }

}
