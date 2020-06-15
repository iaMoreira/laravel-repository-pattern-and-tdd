<?php

namespace App\Http\Controllers;

use App\Repository\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    private $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

}
