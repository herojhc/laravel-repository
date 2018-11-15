# laravel-repository

> * l5-repository simple

## Installation

### Composer

Execute the following command to get the latest version of the package:

```terminal
composer require herojhc/laravel-repository
```

### Laravel

#### >= laravel5.5

ServiceProvider will be attached automatically

#### Other

In your `config/app.php` add `Herojhc\Repositories\Providers\RepositoryServiceProvider::class` to the end of the `providers` array:

```php
'providers' => [
    ...
    Herojhc\Repositories\Providers\RepositoryServiceProvider::class,
],
```

If Lumen

```php
$app->register(Herojhc\Repositories\Providers\LumenRepositoryServiceProvider::class);
```

Publish Configuration

```shell
php artisan vendor:publish --provider "Herojhc\Repositories\Providers\RepositoryServiceProvider"
```

## Methods

### Herojhc\Repositories\Contracts\RepositoryInterface

- all($columns = array('*'))
- first($columns = array('*'))
- paginate($limit = null, $columns = ['*'])
- find($id, $columns = ['*'])
- findBy($field, $value, $columns = ['*'])
- findWhere(array $where, $columns = ['*'])
- findWhereIn($field, array $where, $columns = [*])
- findWhereNotIn($field, array $where, $columns = [*])
- create(array $attributes)
- update(array $attributes, $id)
- updateOrCreate(array $attributes, array $values = [])
- delete($id)
- deleteWhere(array $where)
- orderBy($column, $direction = 'asc');
- with(array $relations);
- has(string $relation);
- whereHas(string $relation, closure $closure);
- hidden(array $fields);
- visible(array $fields);
- scopeQuery(Closure $scope);
- getFieldsSearchable();


### Herojhc\Repositories\Contracts\CriteriaInterface

- pushCriteria($criteria)
- popCriteria($criteria)
- getCriteria()
- getByCriteria(CriteriaInterface $criteria)
- skipCriteria($status = true)
- getFieldsSearchable()


## Usage

### Create a Model

Create your model normally, but it is important to define the attributes that can be filled from the input form data.

```php
namespace App;

class Post extends Eloquent { // or Ardent, Or any other Model Class

    protected $fillable = [
        'title',
        'author',
        ...
     ];

     ...
}
```

### Create a Repository

```php
namespace App;

use Herojhc\Repositories\Eloquent\BaseRepository;

class PostRepository extends BaseRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return "App\\Post";
    }
}
```

### Create a Criteria

#### Using the command

```terminal
php artisan make:criteria My
```

Criteria are a way to change the repository of the query by applying specific conditions according to your needs. You can add multiple Criteria in your repository.

```php

use Herojhc\Repositories\Contracts\RepositoryInterface;
use Herojhc\Repositories\Criteria\Criteria;

class MyCriteria extend Criteria {

    public function apply($model, RepositoryInterface $repository)
    {
        $model = $model->where('user_id','=', Auth::user()->id );
        return $model;
    }
}
```

### Using the Criteria in a Controller

```php

namespace App\Http\Controllers;

use App\PostRepository;

class PostsController extends BaseController {

    /**
     * @var PostRepository
     */
    protected $repository;

    public function __construct(PostRepository $repository){
        $this->repository = $repository;
    }


    public function index()
    {
        $this->repository->pushCriteria(new MyCriteria1());
        $this->repository->pushCriteria(MyCriteria2::class);
        $posts = $this->repository->all();
		...
    }

}
```

