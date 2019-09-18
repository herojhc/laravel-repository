<?php
/**
 * Created by PhpStorm.
 * User: JHC
 * Date: 2018-11-15
 * Time: 11:52
 */

namespace Herojhc\Repositories\Criteria;

use Herojhc\Repositories\Contracts\RepositoryInterface as Repository;
use Illuminate\Support\Facades\Input;

/**
 * Class NoAdmin
 *
 * @package App\Repositories\Criteria
 */
class OrderByCreatedAt extends Criteria
{

    protected $orderBy;
    protected $sortedBy;

    public function __construct($orderBy = null, $sortedBy = null)
    {
        $this->orderBy = $orderBy;
        $this->sortedBy = $sortedBy;
    }

    /**
     * @param    \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model $model
     * @param Repository $repository
     *
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        if (!$this->orderBy && stripos($this->orderBy, "created_at") !== false) {

            if ($model instanceof \Illuminate\Database\Eloquent\Model) {
                return $model->orderBy($model->qualifyColumn('created_at'), $this->sortedBy);
            } else {
                return $model->orderBy($model->getModel()->qualifyColumn('created_at'), $this->sortedBy);
            }
        }
        return $model;

    }

    public function getCriteria()
    {
        $this->orderBy = $this->orderBy ?? Input::get('orderBy', null);
        $this->sortedBy = $this->sortedBy ?? Input::get('sortedBy', 'desc');
    }
}