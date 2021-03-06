<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Helpers\Doctrine;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Splash\Client\Splash;

/**
 * Generic Doctrine Object Crud Helper
 */
trait CrudHelperTrait
{
    /**
     * Doctrine Entity Manager
     *
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * Load Request Object
     *
     * @param string $objectId Object id
     *
     * @return false|object
     */
    public function load($objectId)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Search in Repository
        $entity = $this->repository->find($objectId);
        //====================================================================//
        // Check Object Entity was Found
        if (!$entity) {
            return Splash::log()->errTrace(static::$NAME.' : Unable to load '.$objectId);
        }

        return $entity;
    }

    /**
     * Update Request Object
     *
     * @param array $needed Is This Update Needed
     *
     * @return string Object Id
     */
    public function update($needed)
    {
        //====================================================================//
        // Save
        if ($needed) {
            $this->entityManager->flush($this->object);
        }

        return $this->getObjectIdentifier();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($objectId = null)
    {
        //====================================================================//
        // Try Loading Object to Check if Exists
        $this->object = $this->load($objectId);
        if ($this->object) {
            //====================================================================//
            // Delete
            $this->repository->remove($this->object);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectIdentifier()
    {
        if (empty($this->object)) {
            return false;
        }

        return (string) $this->object->getId();
    }
}
