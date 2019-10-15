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

namespace Splash\Bundle\Helpers\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Exception;
use Splash\Bundle\Connectors\Standalone;
use Splash\Bundle\Services\ConnectorsManager;
use Splash\Client\Splash;

/**
 * Doctrine Events Subscriber to Listen & Commit Objects Changes
 */
abstract class AbstractEventSubscriber implements EventSubscriber
{
    /**
     * List of Entities Managed by Splash
     *
     * @var array
     */
    protected static $entities = array();

    /**
     * Username used for Commits
     *
     * @var array
     */
    protected static $username = "Symfony User";

    /**
     * Username used for Commits
     *
     * @var array
     */
    protected static $commentPrefix = "Entity";

    /**
     * Events Triggers States
     *
     * @var array
     */
    protected static $states = array(
        Events::postPersist => true,
        Events::postUpdate => true,
        Events::preRemove => true,
    );

    /**
     * Splash Connectors Manager
     *
     * @var ConnectorsManager
     */
    private $manager;

    //====================================================================//
    //  CONSTRUCTOR
    //====================================================================//

    /**
     * Service Constructor
     *
     * @param ConnectorsManager $manager
     */
    public function __construct(ConnectorsManager $manager)
    {
        //====================================================================//
        // Store Faker Connector Manager
        $this->manager = $manager;
        //====================================================================//
        // Setup Events
        self::setStates();
        //====================================================================//
        // Safety Check - Ensure Tracked Entities are Given
        if (!is_array(static::$entities) || empty(static::$entities)) {
            throw new Exception("No Tracked Entities Class Defined.");
        }
    }

    //====================================================================//
    //  Subscriber
    //====================================================================//

    /**
     * Return the subscribed events, their methods and priorities
     *
     * @return array
     */
    public function getSubscribedEvents(): array
    {
        return array(
            Events::postPersist => "postPersist",
            Events::postUpdate => "postUpdate",
            Events::preRemove => "preRemove",
        );
    }

    //====================================================================//
    //  Module Actions
    //====================================================================//

    /**
     * Enable/Disable Events Commits
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public static function setStates(bool $persist = true, bool $update = true, bool $remove = true): void
    {
        static::$states = array(
            Events::postPersist => $persist,
            Events::postUpdate => $update,
            Events::preRemove => $remove
        );
    }

    //====================================================================//
    //  Events Actions
    //====================================================================//

    /**
     * On Entity Created Doctrine Event
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postPersist(LifecycleEventArgs $eventArgs): void
    {
        $this->doEventAction(Events::postPersist, $eventArgs, SPL_A_CREATE);
    }

    /**
     * On Entity Updated Doctrine Event
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postUpdate(LifecycleEventArgs $eventArgs): void
    {
        $this->doEventAction(Events::postUpdate, $eventArgs, SPL_A_UPDATE);
    }

    /**
     * On Entity Before Deleted Doctrine Event
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function preRemove(LifecycleEventArgs $eventArgs): void
    {
        $this->doEventAction(Events::preRemove, $eventArgs, SPL_A_DELETE);
    }

    //====================================================================//
    //  Private Methods
    //====================================================================//

    /**
     * Check if Event Commits are Allowed
     *
     * @param string $eventName
     *
     * @return bool
     */
    public static function isEnabled(string $eventName): bool
    {
        if (!isset(static::$states[$eventName])) {
            return false;
        }

        return (bool) static::$states[$eventName];
    }

    /**
     * Safe Get Envent Doctrine Entity Ids
     * Always returns an array of Object Ids
     *
     * @param LifecycleEventArgs $eventArgs
     *
     * @throws Exception
     *
     * @return array
     */
    protected function getObjectIdentifiers(LifecycleEventArgs $eventArgs): array
    {
        //====================================================================//
        // Get Impacted Object Id
        $entity = $eventArgs->getEntity();
        //====================================================================//
        // Safety Check
        if (!method_exists($entity, "getId")) {
            throw new Exception("Managed Entity is Invalid, no Id getter exists.");
        }

        return array((string) $entity->getId());
    }

    /**
     * Check if Entity is managed by Splash
     *
     * @param LifecycleEventArgs $eventArgs
     * @param bool               $connected
     *
     * @return string
     */
    private function isManaged(LifecycleEventArgs $eventArgs): ?string
    {
        //====================================================================//
        // Touch Impacted Entity
        $entity = $eventArgs->getEntity();
        //====================================================================//
        // Walk on Managed Entities
        foreach (static::$entities as $entityClass => $objectType) {
            if (is_a($entity, $entityClass)) {
                return $objectType;
            }
        }

        return null;
    }

    /**
     * On Entity Created Doctrine Event
     *
     * @param LifecycleEventArgs $eventArgs
     */
    private function doEventAction(string $eventName, LifecycleEventArgs $eventArgs, string $action): void
    {
        //====================================================================//
        // Check if Event is Enabled
        if (!$this->isEnabled($eventName)) {
            return;
        }
        //====================================================================//
        // Check if Entity is Managed by Splash
        $objectType = $this->isManaged($eventArgs);
        if (null == $objectType) {
            return;
        }
        //====================================================================//
        // Do Object Change Commit
        $this->doCommit($objectType, $this->getObjectIdentifiers($eventArgs), $action);
    }

    /**
     * Execut Splash Commit for Objects
     *
     * @param string $objectType
     * @param array  $objectIds
     * @param string $action
     */
    private function doCommit(string $objectType, array $objectIds, string $action): void
    {
        //====================================================================//
        // Safety Check
        if (!is_array($objectIds) || empty($objectIds)) {
            return;
        }
        //====================================================================//
        // Locked (Just created) => Skip
        if ((SPL_A_UPDATE == $action) && Splash::Object($objectType)->isLocked()) {
            return;
        }
        //====================================================================//
        //  Search in Configured Servers using Standalone Connector
        $servers = $this->manager->getConnectorConfigurations(Standalone::NAME);
        //====================================================================//
        //  Walk on Configured Servers
        foreach (array_keys($servers) as $serverId) {
            //====================================================================//
            //  Load Connector
            $connector = $this->manager->get((string) $serverId);
            //====================================================================//
            //  Safety Check
            if (null === $connector) {
                continue;
            }
            //====================================================================//
            //  Execute Commit
            $connector->commit(
                $objectType,
                $objectIds,
                $action,
                static::$username,
                sprintf("%s %s - %s", static::$commentPrefix, implode(", ", $objectIds), ucfirst($action))
            );
        }
        //====================================================================//
        // Catch Splash Logs
        $this->manager->pushLogToSession(true);
    }
}
