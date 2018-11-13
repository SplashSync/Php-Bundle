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
     * @return  array|bool
     */
    public function getAvailableObjects() : array;
    
    /**
     * @abstract   Ask for list of available object data
     *              Theses informations are used to setup synchronization
     *
     * @param   string  $ObjectType         Remote Object Type Name.
     *
     * @return  ArrayObject|bool
     * @throws  NotFoundHttpException
     */
    public function getObjectDescription(string $ObjectType);
    
    /**
     * @abstract    Ask for list of available object data
     *              Theses informations are used to setup synchronization
     *
     * @param   string  $ObjectType         Remote Object Type Name.
     *
     * @return  ArrayObject|bool
     * @throws  NotFoundHttpException
     */
    public function getObjectFields(string $ObjectType);

    /**
     * @abstract       Ask for remote slave Informations
     *                 Information list is specific to each node...
     *                 Only "meta" Information is mandatory to read available informations types
     *
     * @param   string  $ObjectType         Object Type Name
     * @param   string  $Filter             Filter for Object List.
     * @param   array   $Params             Listing Parameters
     *                                      $Params->max        ==> Maximum Number of results
     *                                      $Params->offset     ==> Offset for results list
     *                                      $Params->sortfield  ==> Field name for sort list
     *                                      $Params->sortorder  ==> Sort Order for results list (ASC|DESC)
     *
     * @return  ArrayObject|bool
     * @throws  NotFoundHttpException
     */
    public function getObjectList(string $ObjectType, string $Filter = null, array $Params = []);
        
    /**
     * @abstract   Return Remote Object Data with required fields
     *
     * @param   string  $ObjectType         Object Type Name
     * @param   array|string   $Ids         Object Remote Id.
     * @param   array   $List               List of fields to update
     *
     * @return  ArrayObject|bool
     * @throws  NotFoundHttpException
     */
    public function getObject(string $ObjectType, $Ids, array $List);
    
   /**
    * @abstract   Update Remote Customer Data with required fields
    *
    * @param    string  $ObjectType         Object Type Name
    * @param    string  $ObjectId           Object Remote Id.
    * @param    array   $Data               List of fields to update
    *
    * @return   string|bool                 Object Id if success.
    * @throws  NotFoundHttpException
    */
    public function setObject(string $ObjectType, string $ObjectId = null, array $Data = array());
    

    /**
     * @abstract   Delete an object
     *
     * @param   string  $ObjectType         Object Type Name.
     * @param   string  $ObjectId           Customers Remote Id.
     *
     * @return  bool
     * @throws  NotFoundHttpException
     */
    public function deleteObject(string $ObjectType, string $ObjectId);
    
    /**
     * @abstract    Commit an Object Change to Splash Server
     *
     * @param string                    $ObjectType
     * @param ArrayObject|Array|string  $ObjectsIds
     * @param string                    $Action
     * @param string                    $UserName
     * @param string                    $Comment
     *
     * @return void
     */
    public function commit(
        string  $ObjectType,
        $ObjectsIds,
        string  $Action,
        string  $UserName = "Unknown User",
        string  $Comment = ""
    );
}
