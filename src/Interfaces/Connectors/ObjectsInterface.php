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

namespace Splash\Bundle\Interfaces\Connectors;

use ArrayObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Define Required structure for Connectors Objects Access
 */
interface ObjectsInterface
{
    /**
     * Fetch Server Available Objects List
     *
     * @return array
     */
    public function getAvailableObjects(): array;

    /**
     * Ask for list of available object data
     * Theses informations are used to setup synchronization
     *
     * @param string $objectType remote Object Type Name
     *
     * @throws NotFoundHttpException
     *
     * @return array
     */
    public function getObjectDescription(string $objectType): array;

    /**
     * Ask for list of available object data
     * Theses informations are used to setup synchronization
     *
     * @param string $objectType remote Object Type Name
     *
     * @throws NotFoundHttpException
     *
     * @return array
     */
    public function getObjectFields(string $objectType): array;

    /**
     * Ask for remote slave Informations
     * Information list is specific to each node...
     * Only "meta" Information is mandatory to read available informations types
     *
     * @param string $objectType Object Type Name
     * @param string $filter     filter for Object List
     * @param array  $params     Listing Parameters
     *                           $Params->max
     *                           ==> Maximum Number
     *                           of results
     *                           $Params->offset
     *                           ==> Offset for
     *                           results list
     *                           $Params->sortfield
     *                           ==> Field name for
     *                           sort list
     *                           $Params->sortorder
     *                           ==> Sort Order for
     *                           results list
     *                           (ASC|DESC)$Params->max$Params->offset
     *
     * @throws NotFoundHttpException
     *
     * @return array
     */
    public function getObjectList(string $objectType, string $filter = null, array $params = array()): array;

    /**
     * Return Remote Object Data with required fields
     *
     * @param string       $objectType Object Type Name
     * @param array|string $objectIds  object Remote Id
     * @param array        $fieldsList List of fields to update
     *
     * @throws NotFoundHttpException
     *
     * @return array|false
     */
    public function getObject(string $objectType, $objectIds, array $fieldsList);

    /**
     * Update Remote Customer Data with required fields
     *
     * @param string $objectType Object Type Name
     * @param string $objectId   object Remote Id
     * @param array  $objectData List of fields to update
     *
     * @throws NotFoundHttpException
     *
     * @return false|string object Id if success
     */
    public function setObject(string $objectType, string $objectId = null, array $objectData = array());

    /**
     * Delete an object
     *
     * @param string $objectType object Type Name
     * @param string $objectId   customers Remote Id
     *
     * @throws NotFoundHttpException
     *
     * @return bool
     */
    public function deleteObject(string $objectType, string $objectId): bool;

    /**
     * Commit an Object Change to Splash Server
     *
     * @param string                   $objectType
     * @param array|ArrayObject|string $objectsIds
     * @param string                   $action
     * @param string                   $userName
     * @param string                   $comment
     *
     * @return void
     */
    public function commit(string  $objectType, $objectsIds, string  $action, string  $userName = 'Unknown User', string  $comment = '');

    /**
     * Check if This Connector is Self Tracking Objects Changes
     *
     * @return bool
     */
    public function isTrackingConnector(): bool;
}
