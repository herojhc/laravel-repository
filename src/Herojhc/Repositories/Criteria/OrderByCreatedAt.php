<?php
/**
 * Created by PhpStorm.
 * User: JHC
 * Date: 2018-11-15
 * Time: 11:52
 */

namespace Herojhc\Repositories\Criteria;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Herojhc\Repositories\Contracts\RepositoryInterface as Repository;
use Illuminate\Support\Facades\Input;

/**
 * Class NoAdmin
 *
 * @package App\Repositories\Criteria
 */
class OrderByCreatedAt extends Criteria
{

    protected $sortedBy = 'desc';

    public function __construct($sortedBy = 'desc')
    {
        $this->sortedBy = $sortedBy;
    }

    /**
     * @param    Builder|Model $model
     * @param Repository $repository
     *
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        $orderBy = Input::get('orderBy', null);
        if ($orderBy && !stripos($orderBy, 'created_at')) {

            if ($model instanceof Model) {
                return $model->orderBy($model->qualifyColumn('created_at'), $this->sortedBy);
            } else {
                return $model->orderBy($model->getModel()->qualifyColumn('created_at'), $this->sortedBy);
            }
        }
        return $model;

    }
}