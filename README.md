# Conceito
> 
   - MVCS
        - View
        - Controller
        - ~~Service~~ (Repository)  _// vamos abstrair a camada de serviço somente com o Repository Pattern_
        - Model
>


## Instalação

Instale as dependências do framework:

`composer install`

Copie o arquivo de exemplo de configuração `.env.example` para `.env` e edite o que for necessário:  

`cp .env.example .env `

Gere uma nova chave para aplicação:

`php artisan key:generate`

Faça a migração e popule o seu banco de dados:

`php artisan migrate --seed`

Gere o token do JWT:

`php artisan jwt:secret`


## Repository Pattern 

Para fazermos um bom uso do nosso código, decidir particularmente utilizar o padrão [Template Method](https://pt.wikipedia.org/wiki/Template_Method). Não teremos uma explicação muito longa aqui sobre ele, mas podemos entender que com ele criaremos uma base sólida da nossa classe abstrata __Repository__ e utilizaremos as definições dessa _superclasse_ para não precisarmos reimplementar regras que são semelhantes em na maior parte das _subclasse_.  

É muito comum utilizarmos diretamento o __Model__ diretamento no __Controller__, não que isso esteja errado, mas se formos utilizar o _Single responsibility principle_ (Princípio da responsabilidade única) questionamos qual a real responsabilidade do __Controller__? Uma analógia clássica é compararmos a aplicação com um restaurante e o controller com um garçom, vejamos que quando um pedido é realizado pelo cliente (o _frontend_) o garçom leva o pedido para a cozinha, mas ele não é responsável pela preparação do pedido pois ele é somente um intermediário e quanto menos responsabilidade ele estiver, menor são as chances dele se adaptar as outras tarefas sendo expert no que ele é responsável. E então, o que acontece se o código estiver com tanta resposabilidade? Haverão mais modificações no mesmos código e pouco chances de reutilizar as regras estabelicidas naquele código. Isso acaba ferindo os principios mais básicos do Programação Orientada a Objeto, e acaba levando o  programador a codificar uma de forma procedural, focado apenas em resolver problema ali naquele momento do código, sem se importar com o uso daquele mesmo código em outro momento.


```php
// exemplo do que foi falado acima
<?php

namespace App\Http\Controllers;

....

class ClientController extends Controller
{
        public function index()
        {
                return Client::all();
        }

        public function store(Request $request)
        {
                $data = $request->all();
                $client = Client::create($data);
                return $client;
        }
        .....
}

```

Deixamos de conversa e mão ao código. Com Interfaces podermos definir contratos de escôpos de métodos que serão obrigatórios para as classe que os implementam, então assim definiremos nosso `BaseRepository` que garantirar que nossos métodos serão a implementação nas classe de repositorios.

```php
// app/Repositories/Contracts/BaseRepository.php
<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use App\Models\BaseModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BaseRepository
{
    public function findOne(int $id): ?BaseModel;

    public function findOneOrFail(int $id): BaseModel;
    
    public function findOneBy(array $criteria): ?BaseModel;

    public function findBy(array $searchCriteria = []): LengthAwarePaginator;

    public function findIn(string $key, array $values): Collection;

    public function store(array $data): BaseModel;

    public function update(int $id, array $data): BaseModel;

    public function delete(int $id): bool;
}
```

Agora em nossa classe `AbstractRepository` vamos implementar as definições dos escopos da interface `BaseRepository`. Essa classe será criada como uma classe abstrata justamente como manda conceitos do Templat Method, ela só guardará definições para serem reutilizadas em outras classes através da herança.

```php
// app/Repositories/AbstractRepository.php
<?php

namespace App\Repositories;
...
// imports
...
abstract class AbstractRepository implements BaseRepository
{
    use FiltersModelTrait;


    protected $model;

    protected $currentUser;

    public function __construct(BaseModel $model)
    {
        $this->model = $model;
    }

    public function getModel(): BaseModel
    {
        return $this->model;
    }

    public function findOne(int $id): ?BaseModel
    {
        return $this->model->where('id', $id)->first();
    }

    public function findOneOrFail(int $id): BaseModel
    {
        $model = $this->model->where('id', $id)->first();
        if (\is_null($model)) {
            throw new BaseException('API.' . $this->getClassName() . '_not_found', 404);
        }

        return $model;
    }

    public function findOneBy(array $criteria): ?BaseModel
    {
        return $this->model->where($criteria)->first();
    }

    public function findIn(string $key, array $values): Collection
    {
        return $this->model->whereIn($key, $values)->get();
    }

    public function findBy(array $searchCriteria = []): LengthAwarePaginator
    {
        $limit = !empty($searchCriteria['per_page']) ? (int) $searchCriteria['per_page'] : 15; // it's needed for pagination

        $queryBuilder = $this->model->where(function ($query) use ($searchCriteria) {
            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
        });
        ...
        return $queryBuilder->paginate($limit);
    }

    public function store(array $data): BaseModel
    {
        $filledProperties = $this->model->getFillable();
        $keys = array_keys($data);

        foreach ($keys as $key) {
            if (!in_array($key, $filledProperties)) {
                unset($data[$key]);
            }
        }

        $model = $this->model->create($data);
        return $model;
    }

    public function update(int $id, array $data): BaseModel
    {
        $model = $this->findOneOrFail($id);

        $filledProperties = $this->model->getFillable();
        $keys = array_keys($data);
        foreach ($keys as $key) {
            // update only fillAble properties
            if (in_array($key, $filledProperties)) {
                $model->$key = $data[$key];
            }
        }

        $model->save();
        return $model;
    }

    public function delete(int $id): bool
    {
        $model = $this->findOneOrFail($id);
        return $model->delete();
    }

....
}


```

Para esse exemplo criaremos a partir do dominio de Produtos.

`ProductRepositoryInterface`
```php
// app/Repositories/Contracts/ProductRepositoryInterface.php
<?php

namespace App\Repositories\Contracts;

interface ProductRepositoryInterface extends BaseRepository
{
}

```
`ProductRepository`

```php
// app/Repositories/ProductRepository.php
<?php

namespace App\Repositories;

use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductRepository extends AbstractRepository implements ProductRepositoryInterface
{
}

```


`RepositoryServiceProvider`
```php
// app/Providers/RepositoryServiceProvider.php
<?php

namespace App\Providers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\ProductRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
   public function register()
   {
        $this->app->bind(ProductRepositoryInterface::class, function () {
            return new ProductRepository(new Product);
        });

        $this->app->bind(ProductResource::class, function () {
            return new ProductResource(new Product);
        });
    }
}

```

`ProductController`
```php
// app/Http/Controllers/ProductController.php
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

```
