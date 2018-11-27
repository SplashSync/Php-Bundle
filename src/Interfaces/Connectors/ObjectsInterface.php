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
 **/

namespace Splash\Bundle\Interfaces\Connectors;

use ArrayObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @abstract Define Required structure for Connectors Objects Access
 */
interface ObjectsInterface
{
        
    /**
     * @abstract    Fetch Server Available Objects List
     *
     * @return  array
     */
    public function getAvailableObjects() : array;
    
    /**
     * @abstract   Ask for list of available object data
     *              Theses informations are used to setup synchronization
     *
     * @param   string $objectType Remote Object Type Name.
     *
     * @return  array
     *
     * @throws  NotFoundHttpException
     */
    public function getObjectDescription(string $objectType) : array;
    
    /**
     * @abstract    Ask for list of available object data
     *              Theses informations are used to setup synchronization
     *
     * @param   string $objectType Remote Object Type Name.
     *
     * @return  array
     *
     * @throws  NotFoundHttpException
     */
    public function getObjectFields(string $objectType) : array;

    /**
     * @abstract       Ask for remote slave Informations
     *                 Information list is specific to each node...
     *                 Only "meta" Information is mandatory to read available informations types
     *
     * @param   string $objectType Object Type Name
     * @param   string $filter     Filter for Object List.
     * @param   array  $params     Listing Parameters
     *                             $Params->max
     *                             ==> Maximum Number
     *                             of results
     *                             $Params->offset
     *                             ==> Offset for
     *                             results list
     *                             $Params->sortfield
     *                             ==> Field name for
     *                             sort list
     *                             $Params->sortorder
     *                             ==> Sort Order for
     *                             results list
     *                             (ASC|DESC)$Params->max$Params->offset
     *
     * @return  array
     *
     * @throws  NotFoundHttpException
     */
    public function getObjectList(string $objectType, string $filter = null, array $params = []) : array;
        
    /**
     * @abstract   Return Remote Object Data with required fields
     *
     * @param   string       $objectType Object Type Name
     * @param   array|string $objectIds  Object Remote Id.
     * @param   array        $fieldsList List of fields to update
     *
     * @return  array|false
     *
     * @throws  NotFoundHttpException
     */
    public function getObject(string $objectType, $objectIds, array $fieldsList);
    
   /**
    * @abstract   Update Remote Customer Data with required fields
    *
    * @param    string $objectType Object Type Name
    * @param    string $objectId   Object Remote Id.
    * @param    array  $objectData List of fields to update
    *
    * @return   string|false                 Object Id if success.
    *
    * @throws  NotFoundHttpException
    */
    public function setObject(string $objectType, string $objectId = null, array $objectData = array());
    

    /**
     * @abstract   Delete an object
     *
     * @param   string $objectType Object Type Name.
     * @param   string $objectId   Customers Remote Id.
     *
     * @return  bool
     *
     * @throws  NotFoundHttpException
     */
    public function deleteObject(string $objectType, string $objectId) : bool;
    
    /**
     * @abstract    Commit an Object Change to Splash Server
     *
     * @param string                   $objectType
     * @param ArrayObject|Array|string $objectsIds
     * @param string                   $action
     * @param string                   $userName
     * @param string                   $comment
     *
     * @return void
     */
    public function commit(
        string  $objectType,
        $objectsIds,
        string  $action,
        string  $userName = "Unknown User",
        string  $comment = ""
    );
}
