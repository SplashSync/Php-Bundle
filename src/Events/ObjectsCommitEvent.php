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

namespace Splash\Bundle\Events;

use ArrayObject;
use Exception;
use Symfony\Component\EventDispatcher\Event;

/**
 * @abstract    Connectors Object Commit Event
 * This Event is Triggered by Any Connector to Submit Objects Changes to Server.
 */
class ObjectsCommitEvent extends Event
{
    /**
     * Event Name.
     */
    const NAME = 'splash.connectors.commit';

    /**
     * @abstract    WebService Id Of Server Whoo Commit
     *
     * @var string
     */
    private $webserviceId;

    /**
     * @abstract    Object Splash Type Name
     *
     * @var string
     */
    private $objectType;

    /**
     * @abstract    Objects Identifiers (Id)
     *
     * @var array
     */
    private $objectsIds = array();

    /**
     * @abstract    Splash Action Name
     *
     * @var string
     */
    private $action;

    /**
     * @abstract    Username
     *
     * @var string
     */
    private $userName;

    /**
     * @abstract    Action Comment
     *
     * @var string
     */
    private $comment;

    //==============================================================================
    //      EVENT CONSTRUCTOR
    //==============================================================================

    /**
     * @abstract    Event Constructor
     *
     * @param string                   $webserviceId
     * @param string                   $objectType
     * @param array|ArrayObject|string $objectsIds
     * @param string                   $action
     * @param string                   $userName
     * @param string                   $comment
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function __construct(string  $webserviceId, string  $objectType, $objectsIds, string  $action, string  $userName = 'Unknown User', string  $comment = '')
    {
        //==============================================================================
        //      Verify Objects Action Name
        if (!in_array($action, array(SPL_A_CREATE, SPL_A_UPDATE, SPL_A_DELETE), true)) {
            throw new Exception(sprintf('Commit Event : Unknown Action Name Given (%s)', $action));
        }
        //==============================================================================
        //      Objects Ids Storages
        if ($objectsIds instanceof ArrayObject) {
            $this->objectsIds = $objectsIds->getArrayCopy();
        } elseif (is_array($objectsIds)) {
            $this->objectsIds = $objectsIds;
        } elseif (is_scalar($objectType)) {
            $this->objectsIds = array($objectsIds);
        } else {
            throw new Exception('Commit Event : Unknown Objects Ids Given');
        }
        //==============================================================================
        //      Basic Data Strorages
        $this->webserviceId = $webserviceId;
        $this->objectType = $objectType;
        $this->action = $action;
        $this->userName = $userName;
        $this->comment = $comment;
    }

    //==============================================================================
    //      GETTERS & SETTERS
    //==============================================================================

    /**
     * @abstract    Get Webservice Id
     *
     * @return string
     */
    public function getWebserviceId(): string
    {
        return $this->webserviceId;
    }

    /**
     * @abstract    Get Object Type Name
     *
     * @return string
     */
    public function getObjectType(): string
    {
        return $this->objectType;
    }

    /**
     * @abstract    Get Objects Ids Array
     *
     * @return array
     */
    public function getObjectsIds(): array
    {
        return $this->objectsIds;
    }

    /**
     * @abstract    Get Object Action Name
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @abstract    Get Action UserName
     *
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @abstract    Get Action Comment
     *
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }
}
