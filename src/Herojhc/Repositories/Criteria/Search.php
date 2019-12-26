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
use Illuminate\Support\Facades\Config;

/**
 * Class RequestCriteria
 * @package Herojhc\Repositories\Criteria
 */
class Search extends Criteria
{

    protected $search;
    protected $searchFields;
    protected $filter;
    protected $orderBy;
    protected $sortedBy;
    protected $with;
    protected $searchJoin;

    public function __construct($criteria = [])
    {
        $this->search = $criteria['search'] ?? null;
        $this->searchFields = $criteria['searchFields'] ?? null;
        $this->filter = $criteria['filter'] ?? null;
        $this->orderBy = $criteria['orderBy'] ?? null;
        $this->sortedBy = $criteria['sortedBy'] ?? null;
        $this->with = $criteria['with'] ?? null;
        $this->searchJoin = $criteria['searchJoin'] ?? null;
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
        $fieldsSearchable = $repository->getFieldsSearchable();
        list($search,
            $searchFields,
            $filter,
            $orderBy,
            $sortedBy,
            $with,
            $searchJoin) = $this->getCriteria();
        $sortedBy = ($sortedBy == 'ascending' || $sortedBy == 'asc' || $sortedBy == 'ascend') ? 'asc' : 'desc';

        // 获取表名称
        if ($model instanceof Builder) {
            $modelTableName = $model->getModel()->getTable();
        } else {
            $modelTableName = $model->getTable();
        }

        if ($search && is_array($fieldsSearchable) && count($fieldsSearchable)) {
            $searchFields = is_array($searchFields) || is_null($searchFields) ? $searchFields : explode(';', $searchFields);
            $fields = $this->parserFieldsSearch($fieldsSearchable, $searchFields);
            $isFirstField = true;
            $searchData = $this->parserSearchData($search);
            $search = $this->parserSearchValue($search);
            $modelForceAndWhere = strtolower($searchJoin) === 'and';
            $model = $model->where(function ($query) use ($fields, $search, $searchData, $isFirstField, $modelForceAndWhere, $modelTableName) {
                /** @var Builder $query */
                foreach ($fields as $field => $condition) {
                    if (is_numeric($field)) {
                        $field = $condition;
                        $condition = "=";
                    }
                    $value = null;
                    $condition = trim(strtolower($condition));
                    if (isset($searchData[$field])) {
                        $value = ($condition == "like" || $condition == "ilike") ? "%{$searchData[$field]}%" : $searchData[$field];
                    } else {
                        if (!is_null($search)) {
                            $value = ($condition == "like" || $condition == "ilike") ? "%{$search}%" : $search;
                        }
                    }

                    // 模型关联
                    $relation = null;
                    if (stripos($field, '|') !== false) {
                        $explode = explode('|', $field);
                        // 删除数组的最后一个值并返回删除的值
                        $field = array_pop($explode);
                        $relation = implode('.', $explode);
                    } elseif (stripos($field, '.') === false) {
                        $field = $modelTableName . '.' . $field;
                    }

                    if ($isFirstField || $modelForceAndWhere) {
                        if (!is_null($value)) {
                            if (!is_null($relation)) {
                                $query->whereHas($relation, function ($query) use ($field, $condition, $value) {
                                    $query->where($field, $condition, $value);
                                });
                            } else {
                                $query->where($field, $condition, $value);
                            }

                            $isFirstField = false;
                        }
                    } else {
                        if (!is_null($value)) {
                            if (!is_null($relation)) {
                                $query->orWhereHas($relation, function ($query) use ($field, $condition, $value) {
                                    $query->where($field, $condition, $value);
                                });
                            } else {
                                $query->orWhere($field, $condition, $value);
                            }
                        }
                    }
                }
            });
        }
        if (isset($orderBy) && !empty($orderBy)) {
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
                    $sortDirection = ($split[1] == 'ascending' || $split[1] == 'asc' || $split[1] == 'ascend') ? 'asc' : 'desc';
                }
                $model = $model->orderBy($sortColumn, $sortDirection);
            }
        }
        if (isset($filter) && !empty($filter)) {
            if (is_string($filter)) {
                $filter = explode(';', $filter);
            }
            $model = $model->select($filter);
        }
        if ($with) {
            $with = explode(';', $with);
            $model = $model->with($with);
        }
        return $model;
    }

    /**
     * @param $search
     *
     * @return array
     */
    protected function parserSearchData($search)
    {
        $searchData = [];
        if (stripos($search, ':')) {
            $fields = explode(';', $search);
            foreach ($fields as $row) {
                try {
                    list($field, $value) = explode(':', $row);
                    $searchData[$field] = $value;
                } catch (\Exception $e) {
                    //Surround offset error
                }
            }
        }
        return $searchData;
    }

    /**
     * @param $search
     *
     * @return null
     */
    protected function parserSearchValue($search)
    {
        if (stripos($search, ';') || stripos($search, ':')) {
            $values = explode(';', $search);
            foreach ($values as $value) {
                $s = explode(':', $value);
                if (count($s) == 1) {
                    return $s[0];
                }
            }
            return null;
        }
        return $search;
    }

    /**
     * @param array $fields
     * @param array|null $searchFields
     * @return array
     */
    protected function parserFieldsSearch(array $fields = [], array $searchFields = null)
    {
        if (!is_null($searchFields) && count($searchFields)) {
            $acceptedConditions = Config::get('repositories.criteria.acceptedConditions', [
                '=',
                'like'
            ]);
            // searchAbles
            $originalFields = $fields;
            $fields = [];
            foreach ($searchFields as $index => $field) {
                $field_parts = explode(':', $field);
                $temporaryIndex = array_search($field_parts[0], $originalFields);
                if (count($field_parts) == 2) {
                    if (in_array($field_parts[1], $acceptedConditions)) {
                        unset($originalFields[$temporaryIndex]);
                        $field = $field_parts[0];
                        $condition = $field_parts[1];
                        $originalFields[$field] = $condition;
                        $searchFields[$index] = $field;
                    }
                }
            }
            foreach ($originalFields as $field => $condition) {
                if (is_numeric($field)) {
                    $field = $condition;
                    $condition = "=";
                }
                if (in_array($field, $searchFields)) {
                    $fields[$field] = $condition;
                }
            }
            if (count($fields) == 0) {
                return [];
            }
        }
        return $fields;
    }

    protected function getCriteria()
    {
        return [
            $this->search ?? Input::get(Config::get('repositories.criteria.params.search', 'search'), null),
            $this->searchFields ?? Input::get(Config::get('repositories.criteria.params.searchFields', 'searchFields'), null),
            $this->filter ?? Input::get(Config::get('repositories.criteria.params.filter', 'filter'), null),
            $this->orderBy ?? Input::get(Config::get('repositories.criteria.params.orderBy', 'orderBy'), null),
            $this->sortedBy ?? Input::get(Config::get('repositories.criteria.params.sortedBy', 'sortedBy'), 'asc'),
            $this->with ?? Input::get(Config::get('repositories.criteria.params.with', 'with'), null),
            $this->searchJoin ?? Input::get(Config::get('repositories.criteria.params.searchJoin', 'searchJoin'), null)
        ];

    }
}