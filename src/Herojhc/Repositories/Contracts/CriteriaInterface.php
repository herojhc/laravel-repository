<?php

namespace Herojhc\Repositories\Contracts;

use Herojhc\Repositories\Criteria\Criteria;
use Illuminate\Support\Collection;

/**
 * Interface CriteriaInterface
 * @package Herojhc\Repositories\Contracts
 */
interface CriteriaInterface
{

    /**
     * Push Criteria for filter the query
     *
     * @param $criteria
     *
     * @return $this
     */
    public function pushCriteria($criteria);

    /**
     * Pop Criteria
     *
     * @param $criteria
     *
     * @return $this
     */
    public function popCriteria($criteria);

    /**
     * Get Collection of Criteria
     *
     * @return Collection
     */
    public function getCriteria();

    /**
     * Find data by Criteria
     *
     * @param Criteria $criteria
     *
     * @return mixed
     */
    public function getByCriteria(Criteria $criteria);

    /**
     * Skip Criteria
     *
     * @param bool $status
     *
     * @return $this
     */
    public function skipCriteria($status = true);

    /**
     * Reset all Criteria
     *
     * @return $this
     */
    public function resetCriteria();
}
