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

namespace Splash\Bundle\Models\Connectors;

use Exception;
use Splash\Bundle\Interfaces\Objects\TrackingInterface;
use Splash\Bundle\Models\AbstractStandaloneObject;
use Splash\Client\Splash;
use Splash\Models\AbstractObject;
use Splash\Models\Helpers\TestHelper;

/**
 * Manager Access to Generic Splash Objects for Standard Connectors
 *
 * Connector Only Map Objects Type => Classname and Mapper will do the rest
 *
 * Objects must extend Splash\Models\AbstractObject to be used
 * Objects that extend AbstractStandaloneObject will be Configured before use
 *
 * Map is defined on STATIC variable $objectsMap
 */
trait GenericObjectMapperTrait
{
    /**
     * {@inheritdoc}
     */
    public function getAvailableObjects() : array
    {
        //====================================================================//
        // Safety Check => Verify Self-test Pass
        if (!$this->selfTest()) {
            return array();
        }
        //====================================================================//
        // Get Generic Object Types List
        return array_keys(static::$objectsMap);
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectDescription(string $objectType) : array
    {
        //====================================================================//
        // Safety Check => Verify Selftest Pass
        if (!$this->selfTest()) {
            return array();
        }
        //====================================================================//
        // Get Generic Object Type Description
        return $this->getObjectLocalClass($objectType)->description();
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectFields(string $objectType) : array
    {
        //====================================================================//
        // Safety Check => Verify Selftest Pass
        if (!$this->selfTest()) {
            return array();
        }
        //====================================================================//
        // Get Generic Object Fields List
        return $this->getObjectLocalClass($objectType)->fields();
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectList(string $objectType, string $filter = null, array $parameters = array()) : array
    {
        //====================================================================//
        // Safety Check => Verify Selftest Pass
        if (!$this->selfTest()) {
            return array();
        }
        //====================================================================//
        // Get Generic Object Fields List
        $response = $this->getObjectLocalClass($objectType)->objectsList($filter, $parameters);

        return (false === $response) ? array() : $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getObject(string $objectType, $objectIds, array $fieldsList)
    {
        //====================================================================//
        // Safety Check => Verify Selftest Pass
        if (!$this->selfTest()) {
            return false;
        }
        //====================================================================//
        // Safety Checks
        if (empty($objectType)) {
            return false;
        }
        //====================================================================//
        // Take Care of Single Read Requests
        if (is_scalar($objectIds)) {
            return $this->getObjectLocalClass($objectType)->get((string) $objectIds, $fieldsList);
        }
        //====================================================================//
        // Read Multiple Objects at the Same Time
        $response = array();
        foreach ($objectIds as $objectId) {
            $response[$objectId] = $this->getObjectLocalClass($objectType)->get($objectId, $fieldsList);
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function setObject(string $objectType, string $objectId = null, array $objectData = array())
    {
        //====================================================================//
        // Safety Check => Verify Self-test Pass
        if (!$this->selfTest()) {
            return false;
        }

        //====================================================================//
        // Set Generic Object Data
        $response = $this->getObjectLocalClass($objectType)->set($objectId, $objectData);

        //====================================================================//
        // PhpUnit Helper => Submit Object Commit
        if ((false !== $response) && !empty($response) && Splash::isDebugMode()) {
            $action = empty($objectId) ? SPL_A_CREATE : SPL_A_UPDATE;
            TestHelper::simObjectCommit($objectType, $response, $action);
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteObject(string $objectType, string $objectId) : bool
    {
        //====================================================================//
        // Safety Check => Verify Self-test Pass
        if (!$this->selfTest()) {
            return false;
        }
        //====================================================================//
        // Delete Generic Object
        $response = $this->getObjectLocalClass($objectType)->delete($objectId);

        //====================================================================//
        // PhpUnit Helper => Submit Object Commit
        if ((true === $response) && Splash::isDebugMode()) {
            TestHelper::simObjectCommit($objectType, $objectId, SPL_A_DELETE);
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function isObjectTracked(string $objectType): bool
    {
        //====================================================================//
        // Safety Check => Verify Self-test Pass
        if (!$this->selfTest()) {
            return false;
        }
        //====================================================================//
        // Load Object Class
        $objectClass = $this->getObjectLocalClass($objectType);
        //====================================================================//
        // Check if Object Implements Tracking Interface
        return is_subclass_of($objectClass, TrackingInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectTrackingDelay(string $objectType): int
    {
        //====================================================================//
        // Safety Check => Verify Self-test Pass
        if (!$this->selfTest()) {
            return false;
        }
        //====================================================================//
        // Load Object Class
        $objectClass = $this->getObjectLocalClass($objectType);
        //====================================================================//
        // Check if Object is Tracked
        if (is_subclass_of($objectClass, TrackingInterface::class)) {
            //====================================================================//
            // Return Tracking Delay
            return $objectClass->getTrackingDelay();
        }
        //====================================================================//
        // Return Tracking Delay
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectUpdatedIds(string $objectType): array
    {
        //====================================================================//
        // Safety Check => Verify Self-test Pass
        if (!$this->selfTest()) {
            return false;
        }
        //====================================================================//
        // Load Object Class
        $objectClass = $this->getObjectLocalClass($objectType);
        //====================================================================//
        // Check if Object is Tracked
        if (is_subclass_of($objectClass, TrackingInterface::class)) {
            //====================================================================//
            // Return Tracking Delay
            return $objectClass->getUpdatedIds();
        }
        //====================================================================//
        // Return Empty Ids List
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectDeletedIds(string $objectType): array
    {
        //====================================================================//
        // Safety Check => Verify Self-test Pass
        if (!$this->selfTest()) {
            return false;
        }
        //====================================================================//
        // Load Object Class
        $objectClass = $this->getObjectLocalClass($objectType);
        //====================================================================//
        // Check if Object is Tracked
        if (is_subclass_of($objectClass, TrackingInterface::class)) {
            //====================================================================//
            // Return Tracking Delay
            return $objectClass->getDeletedIds();
        }
        //====================================================================//
        // Return Empty Ids List
        return array();
    }

    /**
     * Return a New Intance of Requested Object Type Class
     *
     * @param string $objectType
     *
     * @throws Exception
     *
     * @return AbstractObject
     */
    private function getObjectLocalClass(string $objectType) : AbstractObject
    {
        //====================================================================//
        // Safety Check => Object Type is Mapped
        if (!in_array($objectType, array_keys(self::$objectsMap), true)) {
            throw new Exception(sprintf("Unknown Object Type : %s", $objectType));
        }
        //====================================================================//
        // Get Object Class
        $className = static::$objectsMap[$objectType];
        //====================================================================//
        // Safety Check => Validate Object Class
        if (true !== $this->isValidObjectClass($className)) {
            throw new Exception($this->isValidObjectClass($className));
        }
        //====================================================================//
        // Create Object Class
        $genericObject = new $className($this);
        //====================================================================//
        // If StandaloneObject => Configure it!
        if (is_subclass_of($className, AbstractStandaloneObject::class)) {
            $genericObject->configure($objectType, $this->getWebserviceId(), $this->getConfiguration());
        }

        return $genericObject;
    }

    /**
     * Validate Object Class Name
     *
     * @param mixed $className
     *
     * @return string|true
     */
    private function isValidObjectClass($className)
    {
        //====================================================================//
        // Safety Check => Object Type is String
        if (!is_string($className)) {
            return "Object Type is Not a String";
        }
        //====================================================================//
        // Safety Check => Class Exists
        if (!class_exists($className)) {
            return "Object Class Not Found";
        }
        //====================================================================//
        // Safety Check => Class Extends
        if (!is_subclass_of($className, AbstractObject::class)) {
            return "Object Class MUST extends ".AbstractObject::class;
        }

        return true;
    }
}
