<?php
/**
 * Created by PhpStorm.
 * User: JHC
 * Date: 2018-11-22
 * Time: 15:12
 */

namespace Herojhc\Repositories\Criteria;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Herojhc\Repositories\Contracts\RepositoryInterface as Repository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;

class Keyword extends Criteria
{

    protected $orderBy;
    protected $sortedBy;

    function __construct($orderBy = 'created_at', $sortedBy = 'desc')
    {
        $this->orderBy = $orderBy;
        $this->sortedBy = $sortedBy;
    }

    /**
     * Apply criteria in query repository
     *
     * @param Builder|Model $model
     * @param Repository $repository
     *
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        $search = Input::get(Config::get('repositories.criteria.params.search', 'search'), null);
        $searchFields = Input::get(Config::get('repositories.criteria.params.searchFields', 'searchFields'), null);
        $orderBy = Input::get(Config::get('repositories.criteria.params.orderBy', 'orderBy'), null);
        $sortedBy = Input::get(Config::get('repositories.criteria.params.sortedBy', 'sortedBy'), null);

        if (empty($orderBy)) {
            $orderBy = $this->orderBy;
        }
        if (empty($sortedBy)) {
            $sortedBy = $this->sortedBy;
        }
        // 获取表名称
        if ($model instanceof Builder) {
            $modelTableName = $model->getModel()->getTable();
        } else {
            $modelTableName = $model->getTable();
        }
        if (isset($orderBy) && !empty($orderBy)) {

            $sortedBy = ($sortedBy == 'ascending' || $sortedBy == 'asc') ? 'asc' : 'desc';
            // 查看是否是多条件排序
            $multipleSorts = explode(';', $orderBy);
            // 循环添加排序字段
            foreach ($multipleSorts as $sort) {
                $split = explode('|', $sort);
                $sortColumn = $split[0];
                if (stripos($sortColumn, '.') === false) {
                    $sortColumn = $modelTableName . '.' . $sortColumn;
                }
                $sortDirection = $sortedBy;
                if (count($split) == 2) {
                    $sortDirection = ($split[1] == 'ascending' || $split[1] == 'asc') ? 'asc' : 'desc';
                }
                $model = $model->orderBy($sortColumn, $sortDirection);
            }
        }

        // 查询关键字
        if (!empty($search)) {
            $like = "%{$search}%";

            if (!Str::contains('.', $searchFields)) {
                $searchFields = $modelTableName . '.' . $searchFields;
            }

            $model = $model->where($searchFields, 'LIKE', $like);
        }

        return $model;
    }
}