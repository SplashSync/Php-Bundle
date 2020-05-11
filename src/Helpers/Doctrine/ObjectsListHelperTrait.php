<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2020 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Helpers\Doctrine;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Splash\Client\Splash;

/**
 * Generic Doctrine Object List Helps
 */
trait ObjectsListHelperTrait
{
    /**
     * Default Hydrataion Mode
     *
     * @var int
     */
    private $hydratationMode = Query::HYDRATE_OBJECT;

    /**
     * Return List Of Objects with required filters
     *
     * @param string $filter Filters for Object List.
     * @param array  $params Search parameters for result List.
     *
     * @return array
     */
    public function objectsList($filter = null, $params = null)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();

        //====================================================================//
        // Prepare Query Builder
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->repository->createQueryBuilder('c');
        // Setup Results Offset
        if (isset($params['offset']) && is_numeric($params['offset'])) {
            $queryBuilder->setFirstResult((int) $params['offset']);
        }
        // Limit Results Number
        if (isset($params['max']) && is_numeric($params['max'])) {
            $queryBuilder->setMaxResults((int) $params['max']);
        }
        //====================================================================//
        // Pre Setup Query Builder
        if (method_exists($this, "configureObjectListQueryBuilder")) {
            $this->configureObjectListQueryBuilder($queryBuilder);
        }
        //====================================================================//
        // Add List Filters
        if (!empty($filter) && method_exists($this, "setObjectListFilter")) {
            $this->setObjectListFilter($queryBuilder, $filter);
        }
        //====================================================================//
        // Load Objects List
        $rawData = $queryBuilder->getQuery()->getResult($this->hydratationMode);
        //====================================================================//
        // Parse Data on Result Array
        $response = array();
        foreach ($rawData as $object) {
            $response[] = $this->getObjectListArray($object);
        }
        //====================================================================//
        // Parse Meta Infos on Result Array
        $response['meta'] = array(
            'total' => $this->getTotalCount(),
            'current' => count($rawData),
        );
        //====================================================================//
        // Return result
        return $response;
    }

    /**
     * Set Object List Hydrataion Mode
     *
     * @param int $hydratationMode
     *
     * @return self
     */
    protected function setObjectHydratationMode(int $hydratationMode): self
    {
        $this->hydratationMode = $hydratationMode;

        return $this;
    }

    /**
     * Get Total Object Count
     *
     * @return int
     */
    private function getTotalCount(): int
    {
        $queryBuilder = $this->repository->createQueryBuilder('c');
        //====================================================================//
        // Pre Setup Query Builder
        if (method_exists($this, "configureObjectListQueryBuilder")) {
            $this->configureObjectListQueryBuilder($queryBuilder);
        }

        return $queryBuilder
            ->select('count(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
