<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2018 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

/**
 * @abstract    Local Overriding Objects Manager for Splash Bundle
 *
 * @author      B. Paquier <contact@splashsync.com>
 */

namespace Splash\Local\Objects;

use ArrayObject;
use Splash\Bundle\Interfaces\ConnectorInterface;
use Splash\Core\SplashCore  as Splash;
use Splash\Models\Objects\LockTrait;
use Splash\Models\Objects\ObjectInterface;

/**
 * @abstract    Splash Bundle Connectors Objects Access
 */
class Manager implements ObjectInterface
{
    use LockTrait;
    
    /**
     *  Object Name
     */
    protected static $NAME            =  __CLASS__;
    
    /**
     * @var ConnectorInterface
     */
    private $connector;
    
    /**
     * @var string
     */
    private $objectType;
    
    //====================================================================//
    // Class Constructor
    //====================================================================//
        
    /**
     * @abstract       Init a New Object Manager
     *
     * @param   ConnectorInterface $connector
     * @param   string             $objectType
     *
     * @return  void
     */
    public function __construct(ConnectorInterface $connector, string $objectType)
    {
        $this->connector    =   $connector;
        $this->objectType   =   $objectType;
    }
    
    //====================================================================//
    //  COMMON CLASS INFORMATIONS
    //====================================================================//
    
    /**
     * {@inheritdoc}
     */
    public static function getIsDisabled()
    {
        return false;
    }
    
    //====================================================================//
    // Class Main Functions
    //====================================================================//
    
    /**
     * {@inheritdoc}
     */
    public function description()
    {
        //====================================================================//
        // Forward Action
        return $this->connector->getObjectDescription($this->objectType);
    }
        
    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        //====================================================================//
        // Forward Action
        return $this->connector->getObjectFields($this->objectType);
    }
    
    /**
     * {@inheritdoc}
     */
    public function objectsList($filter = null, $params = null)
    {
        //====================================================================//
        // Forward Action
        return $this->connector->getObjectList($this->objectType, $filter, self::toArray($params));
    }
    
    /**
     * {@inheritdoc}
     */
    public function get($objectIds = null, $fieldsList = null)
    {
        //====================================================================//
        // Safety Check
        if (is_null($objectIds)) {
            return false;
        }
        //====================================================================//
        // Forward Action
        return $this->connector->getObject($this->objectType, $objectIds, self::toArray($fieldsList));
    }
        
    /**
     * {@inheritdoc}
     */
    public function set($objectId = null, $data = null)
    {
        //====================================================================//
        // Safety Check
        if (is_null($data)) {
            return false;
        }
        //====================================================================//
        // Forward Action
        return $this->connector->setObject($this->objectType, $objectId, self::toArray($data));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($objectId = null)
    {
        //====================================================================//
        // Safety Check
        if (is_null($objectId)) {
            return true;
        }
        //====================================================================//
        // Forward Action
        return $this->connector->deleteObject($this->objectType, $objectId);
    }
    
    //====================================================================//
    // Tooling Functions
    //====================================================================//
       
    /**
     * @abstract    Normalize Array or ArrayObject to Array
     *
     * @param null|array|ArrayObject $data
     *
     * @return array
     */
    private static function toArray($data) : array
    {
        if (($data instanceof ArrayObject)) {
            return $data->getArrayCopy();
        }
        if (is_null($data) || empty($data)) {
            return array();
        }

        return $data;
    }
}
