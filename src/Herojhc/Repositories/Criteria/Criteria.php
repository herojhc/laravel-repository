<?php

namespace Herojhc\Repositories\Criteria;

use Herojhc\Repositories\Contracts\RepositoryInterface as Repository;

abstract class Criteria
{

    /**
     * @param $model
     * @param Repository $repository
     * @return mixed
     */
    public abstract function apply($model, Repository $repository);
}
