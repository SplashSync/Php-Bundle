<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2019 Splash Sync  <www.splashsync.com>
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
use Splash\Bundle\Models\AbstractStandaloneObject;
use Splash\Models\AbstractObject;
use Splash\Models\Helpers\TestHelper;

/**
 * Manager Access to Generic Splash Objects for Stantard Connectors
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
//    /**
//     * Objects Type Class Map
//     *
//     * @var array
//     */
//    protected static $objectsMap = array();

    /**
     * {@inheritdoc}
     */
    public function getAvailableObjects() : array
    {
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
            return $this->getObjectLocalClass($objectType)->get($objectType, (string) $objectIds, $fieldsList);
        }
        //====================================================================//
        // Read Multiple Objects at the Same Time
        $response = array();
        foreach ($objectIds as $objectId) {
            $response[$objectId] = $this->getObjectLocalClass($objectType)->get($objectType, $objectId, $fieldsList);
        }
        
        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function setObject(string $objectType, string $objectId = null, array $objectData = array())
    {
        //====================================================================//
        // Safety Check => Verify Selftest Pass
        if (!$this->selfTest()) {
            return false;
        }
        
        //====================================================================//
        // Set Generic Object Data
        $response = $this->getObjectLocalClass($objectType)->set($objectId, $objectData);
        
        //====================================================================//
        // PhpUnit Helper => Submit Object Commit
        if ((false !== $response) && !empty($response)) {
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
        // Safety Check => Verify Selftest Pass
        if (!$this->selfTest()) {
            return false;
        }
        //====================================================================//
        // Delete Generic Object
        $response = $this->getObjectLocalClass($objectType)->delete($objectId);
        
        //====================================================================//
        // PhpUnit Helper => Submit Object Commit
        if (true === $response) {
            TestHelper::simObjectCommit($objectType, $objectId, SPL_A_DELETE);
        }
        
        return $response;
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
            throw new Exception("Unknown Object Type : " . $objectType);
        }
        //====================================================================//
        // Get Object Class
        $className = static::$objectsMap[$objectType];
        //====================================================================//
        // Safety Check => Validate Object Class
        if (true !== $this->isValidClass($className)) {
            throw new Exception($this->isValidClass($className));
        }
        //====================================================================//
        // Create Object Class
        $genericObject = new $className();
        //====================================================================//
        // If StandaloneObject => Configure it!
        if (!is_subclass_of($className, AbstractStandaloneObject::class)) {
            $genericObject->configure($this->getConfiguration());
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
    private function isValidClass($className)
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
            return "Object Class MUST extends " . AbstractObject::class;
        }

        return true;
    }
}
