<?php

namespace App\Providers;

use App\Http\Resources\ClientResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\UserResource;
use App\Models\Client;
use App\Models\Product;
use App\Models\User;
use App\Repositories\AbstractRepository;
use App\Repositories\ClientRepository;
use App\Repositories\Contracts\BaseRepository;
use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Repositories\Contracts\LoginRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\RegisterRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\LoginRepository;
use App\Repositories\ProductRepository;
use App\Repositories\RegisterRepository;
use App\Repositories\UserRepository;
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

        $this->app->bind(LoginRepositoryInterface::class, LoginRepository::class);

        $this->app->bind(ClientRepositoryInterface::class, function () {
            return new ClientRepository(new Client);
        });

        $this->app->bind(ClientResource::class, function () {
            return new ClientResource(new Client);
        });

        $this->app->bind(ProductRepositoryInterface::class, function () {
            return new ProductRepository(new Product);
        });

        $this->app->bind(ProductResource::class, function () {
            return new ProductResource(new Product);
        });

        $this->app->bind(UserRepositoryInterface::class, function () {
            return new UserRepository(new User);
        });

        $this->app->bind(UserResource::class, function () {
            return new UserResource(new User);
        });


        $this->app->bind(RegisterRepositoryInterface::class, RegisterRepository::class);
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
