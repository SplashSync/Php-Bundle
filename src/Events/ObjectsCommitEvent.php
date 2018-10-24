<?php

/**
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
 *
 * @author Bernard Paquier <contact@splashsync.com>
 */

namespace Splash\Bundle\Events;

use Exception;
use ArrayObject;
use Symfony\Component\EventDispatcher\Event;

/**
 * Connectors Object Commit Event
 * This Event is Triggered by Any Connector to Submit Objects Changes to Server
 */
class ObjectsCommitEvent extends Event
{

    /**
     * Event Name
     */
    const NAME  =   "splash.connectors.commit";

    /**
     * @abstract    WebService Id Of Server Whoo Commit
     * @var string
     */
    private $ServerId = null;

    /**
     * @abstract    Object Splash Type Name
     * @var string
     */
    private $ObjectType = null;
    
    /**
     * @abstract    Objects Identifiers (Id)
     * @var array
     */
    private $ObjectsIds = array();

    /**
     * @abstract    Splash Action Name
     * @var string
     */
    private $Action = null;

    /**
     * @abstract    Username
     * @var string
     */
    private $UserName = null;
    
    /**
     * @abstract    Action Comment
     * @var string
     */
    private $Comment = null;

    //==============================================================================
    //      EVENT CONSTRUCTOR
    //==============================================================================

    /**
     * @abstract    Event Constructor
     *
     * @param string                    $ServerId
     * @param string                    $ObjectType
     * @param string|ArrayObject|Array  $ObjectsIds
     * @param string                    $Action
     * @param string                    $UserName
     * @param string                    $Comment
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function __construct(
        string  $ServerId,
        string  $ObjectType,
        $ObjectsIds,
        string  $Action,
        string  $UserName = "Unknown User",
        string  $Comment = ""
    ) {
        //==============================================================================
        //      Verify Objects Action Name
        if (!in_array($Action, [ SPL_A_CREATE, SPL_A_UPDATE, SPL_A_DELETE ])) {
            throw new Exception("Commit Event : Unknown Action Name Given (" . $Action . ")");
        }
        //==============================================================================
        //      Objects Ids Storages
        if (is_a("ArrayObject", $ObjectsIds)) {
            $this->ObjectsIds   =   $ObjectsIds->getArrayCopy();
        } elseif (is_array($ObjectsIds)) {
            $this->ObjectsIds   =   $ObjectsIds;
        } elseif (is_scalar($ObjectType)) {
            $this->ObjectsIds   =   array($ObjectsIds);
        } else {
            throw new Exception("Commit Event : Unknown Objects Ids Given (" . get_class($ObjectsIds) . ")");
        }
        //==============================================================================
        //      Basic Data Strorages
        $this->ServerId     =   $ServerId;
        $this->ObjectType   =   $ObjectType;
        $this->Action       =   $Action;
        $this->UserName     =   $UserName;
        $this->Comment      =   $Comment;
    }
    
    
    //==============================================================================
    //      GETTERS & SETTERS
    //==============================================================================
        
    /**
     * @abstract    Get Server Id
     * @return string
     */
    public function getServerId() : string
    {
        return $this->ServerId;
    }

    /**
     * @abstract    Get Object Type Name
     * @return string
     */
    public function getObjectType() : string
    {
        return $this->ObjectType;
    }

    /**
     * @abstract    Get Objects Ids Array
     * @return array
     */
    public function getObjectsIds() : array
    {
        return $this->ObjectsIds;
    }
    
    /**
     * @abstract    Get Object Action Name
     * @return string
     */
    public function getAction()
    {
        return $this->Action;
    }

        /**
     * @abstract    Get Action UserName
     * @return string
     */
    public function getUserName() : string
    {
        return $this->UserName;
    }

    /**
     * @abstract    Get Action Comment
     * @return string
     */
    public function getComment() : string
    {
        return $this->Comment;
    }
}
