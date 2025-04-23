<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Helpers\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Splash\Core\Client\Splash;

/**
 * Generic Doctrine Object Crud Helper
 */
trait CrudHelperTrait
{
    /**
     * Doctrine Entity Manager
     */
    protected EntityManagerInterface $entityManager;

    /**
     * Doctrine Object Repository
     */
    protected ObjectRepository $repository;

    /**
     * Load Request Object
     */
    public function load(string $objectId): ?object
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
            return Splash::log()->errNull(static::$name.' : Unable to load '.$objectId);
        }

        return $entity;
    }

    /**
     * Update Request Object
     *
     * @param bool $needed Is This Update Needed
     *
     * @return null|string Object ID
     */
    public function update(bool $needed): ?string
    {
        //====================================================================//
        // Save
        if ($needed) {
            $this->entityManager->flush();
        }

        return $this->getObjectIdentifier();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $objectId): bool
    {
        //====================================================================//
        // Try Loading Object to Check if Exists
        $object = $this->load($objectId);
        if ($object) {
            //====================================================================//
            // Delete
            $this->entityManager->remove($object);
            $this->entityManager->flush();
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectIdentifier(): ?string
    {
        if (empty($this->object)) {
            return null;
        }
        //====================================================================//
        // Get Generic
        if (method_exists($this->object, "getId")) {
            return (string) $this->object->getId();
        }

        //====================================================================//
        // Get Property
        return (string) $this->object->id ?? null;
    }
}
