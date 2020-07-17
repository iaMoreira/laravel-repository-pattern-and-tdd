<?php

namespace App\Providers;

use App\Http\Resources\ClientResource;
use App\Http\Resources\ProductResource;
use App\Models\Client;
use App\Models\Product;
use App\Repositories\AbstractRepository;
use App\Repositories\ClientRepository;
use App\Repositories\Contracts\BaseRepository;
use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\ProductRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(BaseRepository::class, AbstractRepository::class);

        // $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, function () {
            return new ProductRepository(new Product);
        });

        $this->app->bind(ProductResource::class, function () {
            return new ProductResource(new Product);
        });

        $this->app->bind(ClientRepositoryInterface::class, function () {
            return new ClientRepository(new Client);
        });

        $this->app->bind(ClientResource::class, function () {
            return new ClientResource(new Client);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
