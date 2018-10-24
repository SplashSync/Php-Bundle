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
use Splash\Models\AbstractObject;

/**
 * @abstract Define Required structure for Connectors Objects Access
 */
interface ObjectsInterface
{
    
    /**
     * @abstract    Fetch Server Available Objects List
     *
     * @return     ArrayObject|bool
     */
    public function objects();
    
    /**
     * @abstract   Get Server Objects Local Class
     *
     * @param   string  $ObjectType         Remote Object Type Name.
     *
     * @return  AbstractObject
     * @throws  NotFoundHttpException
     */
    public function object(string $ObjectType) : AbstractObject;
    
        
    /**
     * @abstract    Fetch Server Available Objects List
     *
     * @param   array   $Config             Connector Configuration
     *
     * @return     ArrayObject|bool
     */
    public function getAvailableObjects(array $Config);
    
//    /**
//     * @abstract   Get Server Objects Local Class
//     *
//     * @param   string  $ObjectType         Remote Object Type Name.
//     *
//     * @return  AbstractObject
//     * @throws  NotFoundHttpException
//     */
//    public function getObjectLocalClass( string $ObjectType );
//
    /**
     * @abstract   Ask for list of available object data
     *              Theses informations are used to setup synchronization
     *
     * @param   array   $Config             Connector Configuration
     * @param   string  $ObjectType         Remote Object Type Name.
     *
     * @return  ArrayObject|bool
     * @throws  NotFoundHttpException
     */
    public function getObjectDescription(array $Config, string $ObjectType);
    
    /**
     * @abstract    Ask for list of available object data
     *              Theses informations are used to setup synchronization
     *
     * @param   array   $Config             Connector Configuration
     * @param   string  $ObjectType         Remote Object Type Name.
     *
     * @return  ArrayObject|bool
     * @throws  NotFoundHttpException
     */
    public function getObjectFields(array $Config, string $ObjectType);

    /**
     * @abstract       Ask for remote slave Informations
     *                 Information list is specific to each node...
     *                 Only "meta" Information is mandatory to read available informations types
     *
     * @param   array   $Config             Connector Configuration
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
    public function getObjectList(array $Config, string $ObjectType, string $Filter = null, array $Params = []);
        
    /**
     * @abstract   Return Remote Object Data with required fields
     *
     * @param   array   $Config             Connector Configuration
     * @param   string  $ObjectType         Object Type Name
     * @param   mixed   $Ids                Object Remote Id.
     * @param   array   $List               List of fields to update
     *
     * @return  ArrayObject|bool
     * @throws  NotFoundHttpException
     */
    public function getObject(array $Config, string $ObjectType, $Ids, array $List);
  
    
   /**
    * @abstract   Update Remote Customer Data with required fields
    *
    * @param    array   $Config             Connector Configuration
    * @param    string  $ObjectType         Object Type Name
    * @param    string  $ObjectId           Object Remote Id.
    * @param    array   $Data               List of fields to update
    *
    * @return   string|bool                 Object Id if success.
     * @throws  NotFoundHttpException
    */
    public function setObject(array $Config, string $ObjectType, $ObjectId, array $Data);
    
//
//    /**
//     * @abstract   Delete an object
//     *
//     * @param   Node    $Node               WebService Remote Node Object
//     * @param   string  $ObjectType         Object Type Name.
//     * @param   string  $ObjectId           Customers Remote Id.
//     * @param   bool    $Queue              Ask for Queuing of this task
//     *
//     * @return  bool
//     * @throws  NotFoundHttpException
//     */
//    public function deleteObject(Node $Node, string $ObjectType, string $ObjectId, bool $Queue = False);
//
}
