<?php

namespace App\Providers;

use App\Client;
use App\Product;
use App\Repository\ClientRepository;
use App\Repository\ProductRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ClientRepository::class, function () {
            return new ClientRepository(new Client());
        });

        $this->app->bind(ProductRepository::class, function () {
            return new ProductRepository(new Product());
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
