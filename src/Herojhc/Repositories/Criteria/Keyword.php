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
    protected $search;
    protected $searchFields;

    function __construct($criteria = [])
    {
        $this->orderBy = $criteria['orderBy'] ?? null;
        $this->sortedBy = $criteria['sortedBy'] ?? null;
        $this->search = $criteria['search'] ?? null;
        $this->searchFields = $criteria['searchFields'] ?? null;
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

        $this->getCriteria();
        // 获取表名称
        if ($model instanceof Builder) {
            $modelTableName = $model->getModel()->getTable();
        } else {
            $modelTableName = $model->getTable();
        }
        if (!empty($this->orderBy)) {

            $sortedBy = ($this->sortedBy == 'ascending' || $this->sortedBy == 'asc') ? 'asc' : 'desc';
            // 查看是否是多条件排序
            $multipleSorts = explode(';', $this->orderBy);
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
        if (!empty($this->search)) {
            $like = "%{$this->search}%";

            if (!Str::contains('.', $this->searchFields)) {
                $this->searchFields = $modelTableName . '.' . $this->searchFields;
            }

            $model = $model->where($this->searchFields, 'LIKE', $like);
        }

        return $model;
    }

    protected function getCriteria()
    {
        $this->search = $this->search ?? Input::get(Config::get('repositories.criteria.params.search', 'search'), null);
        $this->searchFields = $this->searchFields ?? Input::get(Config::get('repositories.criteria.params.searchFields', 'searchFields'), null);
        $this->orderBy = $this->orderBy ?? Input::get(Config::get('repositories.criteria.params.orderBy', 'orderBy'), null);
        $this->sortedBy = $this->sortedBy ?? Input::get(Config::get('repositories.criteria.params.sortedBy', 'sortedBy'), null);
    }
}