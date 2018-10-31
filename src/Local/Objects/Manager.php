<?php
/*
 * This file is part of SplashSync Project.
 *
 * Copyright (C) Splash Sync <www.splashsync.com>
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @abstract    Local Overriding Objects Manager for Splash Bundle
 * @author      B. Paquier <contact@splashsync.com>
 */

namespace Splash\Local\Objects;

use ArrayObject;

use Splash\Core\SplashCore  as Splash;

use Splash\Models\Objects\LockTrait;
use Splash\Models\Objects\ObjectInterface;

use Splash\Bundle\Interfaces\ConnectorInterface;

/**
 * @abstract    Splash Bundle Connectors Objects Access
 */
class Manager implements ObjectInterface
{
    use LockTrait;
    
    /**
     * @var ConnectorInterface
     */
    private $Connector      = null;
    
    /**
     * @var string
     */
    private $ObjectType     = null;
    
    /**
     *  Object Name
     */
    protected static $NAME            =  __CLASS__;
    
    //====================================================================//
    // Class Constructor
    //====================================================================//
        
    /**
     * @abstract       Init a New Object Manager
     * @param   ConnectorInterface  $Connector
     * @param   string              $ObjectType
     * @return  void
     */
    public function __construct(ConnectorInterface $Connector, string $ObjectType)
    {
        $this->Connector    =   $Connector;
        $this->ObjectType   =   $ObjectType;
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
        return $this->Connector->getObjectDescription($this->ObjectType);
    }
        
    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        //====================================================================//
        // Forward Action
        return $this->Connector->getObjectFields($this->ObjectType);
    }
    
    /**
     * {@inheritdoc}
     */
    public function objectsList($filter = null, $params = null)
    {
        //====================================================================//
        // Forward Action
        return $this->Connector->getObjectList($this->ObjectType, $filter, self::toArray($params));
    }
    
    /**
     *  @abstract     Reading of requested Object Data
     *
     *  @param      array|string    $ObjectIds      Objects Id.
     *  @param      array           $FieldsList     List of requested fields
     *
     *  @return     array|false
     */
    public function get($ObjectIds = null, $FieldsList = null)
    {
        //====================================================================//
        // Safety Check
        if (is_null($ObjectIds)) {
            return false;
        }
        //====================================================================//
        // Forward Action
        return $this->Connector->getObject($this->ObjectType, $ObjectIds, self::toArray($FieldsList));
    }
        
    /**
     *  @abstract     Write or Create requested Object Data
     *
     *  @param      string  $ObjectId       Object Id.  If NULL, Object needs to be created.
     *  @param      array   $Data           List of Fields to Write
     *
     *  @return     string|false            Object Id.  If False, Object wasn't created.
     */
    public function set($ObjectId = null, $Data = null)
    {
        //====================================================================//
        // Safety Check
        if (is_null($Data)) {
            return false;
        }
        //====================================================================//
        // Forward Action
        return $this->Connector->setObject($this->ObjectType, $ObjectId, self::toArray($Data));
    }

    /**
    *   @abstract   Delete requested Object
     *  @param      array   $ObjectId       Object Id
    *   @return     bool
    */
    public function delete($ObjectId = null)
    {
        //====================================================================//
        // Forward Action
        return $this->Connector->deleteObject($this->ObjectType, $ObjectId);
    }
    
    //====================================================================//
    // Tooling Functions
    //====================================================================//
        
    private static function toArray($Data)
    {
        if (is_a($Data, ArrayObject::class)) {
            return $Data->getArrayCopy();
        }
        if (is_null($Data)) {
            return array();
        }
        return $Data;
    }
}
