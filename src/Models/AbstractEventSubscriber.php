<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Models;

use Exception;
use Splash\Bundle\Connectors\Standalone;
use Splash\Bundle\Services\ConnectorsManager;
use Splash\Core\Client\Splash;
use Splash\Core\Dictionary\SplOperations;
use Splash\Local\Local;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Core Class for Events Subscriber to Listen & Splash Commit Objects Changes
 */
abstract class AbstractEventSubscriber
{
    /**
     * List of Entities Managed by Splash
     * - key:     Class Name
     * - value:   Splash Object Type Name
     *
     * @var array<class-string, string>
     */
    protected static array $classMap = array();

    /**
     * List of SubscribedEvents
     *
     * @var array
     */
    protected static array $subscribedEvents = array();

    /**
     * Username used for Commits
     *
     * @var string
     */
    protected static string $username = "Symfony User";

    /**
     * Username used for Commits
     *
     * @var string
     */
    protected static string $commentPrefix = "Object";

    /**
     * Events Triggers States
     *
     * @var array
     */
    protected static array $disabledStates = array();

    /**
     * Splash Connectors Manager
     *
     * @var ConnectorsManager
     */
    private ConnectorsManager $manager;

    //====================================================================//
    //  CONSTRUCTOR
    //====================================================================//

    /**
     * Service Constructor
     *
     * @param ConnectorsManager $manager
     *
     * @throws Exception
     */
    public function __construct(ConnectorsManager $manager)
    {
        //====================================================================//
        // Store Faker Connector Manager
        $this->manager = $manager;
        //====================================================================//
        // Safety Check - Ensure Tracked Entities are Given
        if (empty(static::$classMap)) {
            throw new Exception("No Tracked Objects Class Defined.");
        }
        //====================================================================//
        // Safety Check - Ensure Event Names are Given
        if (empty(static::$subscribedEvents)) {
            throw new Exception("No Tracked Events Names Defined.");
        }
    }

    //====================================================================//
    //  State Manager
    //====================================================================//

    /**
     * Enable/Disable Commits on Given Events Names
     *
     * @param string $eventName
     * @param bool   $status
     *
     * @return void
     */
    public static function setState(string $eventName, bool $status): void
    {
        $eventName = static::class.":".$eventName;
        if ($status && isset(static::$disabledStates[$eventName])) {
            unset(static::$disabledStates[$eventName]);
        }

        if (!$status && !isset(static::$disabledStates[$eventName])) {
            static::$disabledStates[$eventName] = true;
        }
    }

    /**
     * Enable All Events when Running the Installer
     */
    public static function setAllStatesEnabled(): void
    {
        foreach (array_keys(static::$subscribedEvents) as $eventName) {
            static::setState($eventName, true);
        }
    }

    /**
     * Disable All Events when Running the Installer
     */
    public static function setAllStatesDisabled(): void
    {
        foreach (array_keys(static::$subscribedEvents) as $eventName) {
            static::setState($eventName, false);
        }
    }

    /**
     * Check if Event Commits are Allowed for an Event name
     *
     * @param string $eventName
     *
     * @return bool
     */
    public static function isStateEnabled(string $eventName): bool
    {
        $eventName = static::class.":".$eventName;

        return !isset(static::$disabledStates[$eventName]);
    }

    //====================================================================//
    //  Methods to Overrides
    //====================================================================//

    /**
     * Detect Object Type from Received Event
     * Null Types will Filter Events from Beginning
     *
     * @param GenericEvent $event
     *
     * @return null|string
     */
    protected function getObjectType(GenericEvent $event): ?string
    {
        $subject = $event->getSubject();

        return is_object($subject)
            ? self::isInClassMap(get_class($subject))
            : null
        ;
    }

    /**
     * Safe Get Event Objects Ids
     * Always returns an array of Object Ids
     *
     * @param GenericEvent      $event
     * @param AbstractConnector $connector
     *
     * @throws Exception
     *
     * @return string[]
     *
     * @SuppressWarnings(UnusedFormalParameter)
     */
    protected function getObjectIdentifiers(GenericEvent $event, AbstractConnector $connector): array
    {
        //====================================================================//
        // Get Impacted Object ID
        $subject = $event->getSubject();
        $objectIds = array();
        foreach (is_array($subject) ? $subject : array($subject) as $object) {
            //====================================================================//
            // Check if Object is Managed
            if (!is_object($object) || !self::isInClassMap(get_class($object))) {
                continue;
            }
            //====================================================================//
            // Safety Check
            if (!method_exists($object, "getId")) {
                throw new Exception("Managed Object is Invalid, no Id getter exists.");
            }
            $objectIds[] = (string) $object->getId();
        }

        return $objectIds;
    }

    //====================================================================//
    //  Tooling Methods
    //====================================================================//

    /**
     * Check if Object is Managed by Splash
     *
     * @param string $className
     *
     * @return null|string
     */
    protected function isInClassMap(string $className): ?string
    {
        //====================================================================//
        // Walk on Managed Entities
        foreach (static::$classMap as $entityClass => $objectType) {
            if (is_a($className, $entityClass, true)) {
                return $objectType;
            }
        }

        return null;
    }

    /**
     * On Entity Created Doctrine Event
     *
     * @param string       $eventName
     * @param GenericEvent $event
     * @param string       $action
     *
     * @return void
     */
    protected function doEventAction(string $eventName, GenericEvent $event, string $action): void
    {
        //====================================================================//
        // Check if This Event Should be Triggered
        if (!static::isStateEnabled($eventName)) {
            return;
        }
        //====================================================================//
        // Check if Object Type is Valid
        $objectType = $this->getObjectType($event);
        if (empty($objectType)) {
            return;
        }
        //====================================================================//
        // Do Object Change Commit
        $this->doCommit($event, $objectType, $action);
    }

    //====================================================================//
    //  Private Methods
    //====================================================================//

    /**
     * Execute Splash Commit for Objects
     *
     * @param GenericEvent $event
     * @param string       $objectType
     * @param string       $action
     */
    private function doCommit(GenericEvent $event, string $objectType, string $action): void
    {
        //====================================================================//
        //  Search in Configured Servers using Standalone Connector
        $servers = $this->manager->getConnectorConfigurations(Standalone::NAME);
        //====================================================================//
        //  Walk on Configured Servers
        foreach (array_keys($servers) as $serverId) {
            //====================================================================//
            //  Execute Commit to Server
            try {
                $this->doServerCommit($event, $serverId, $objectType, $action);
            } catch (Exception $e) {
                Splash::log()->report($e);
            }
        }
        //====================================================================//
        // Catch Splash Logs
        $this->manager->pushLogToSession(true);
    }

    /**
     * Execute Splash Commit for Objects
     *
     * @param GenericEvent $event
     * @param string       $serverId
     * @param string       $objectType
     * @param string       $action
     *
     * @throws Exception
     */
    private function doServerCommit(GenericEvent $event, string $serverId, string $objectType, string $action): void
    {
        //====================================================================//
        //  Load Connector
        $connector = $this->manager->get((string) $serverId);
        //====================================================================//
        //  Safety Check
        if (null === $connector) {
            return;
        }
        //====================================================================//
        // Setup Splash Local Class
        $local = Splash::local();
        if (($local instanceof Local) && empty($local->getServerId())) {
            $local->setServerId($serverId);
        }
        //====================================================================//
        // Locked (Just created) => Skip
        if ((SplOperations::UPDATE == $action) && Splash::Object($objectType)->isLocked()) {
            return;
        }
        //====================================================================//
        //  Transform Entities to Object Ids
        $objectIds = $this->getObjectIdentifiers($event, $connector);
        //====================================================================//
        // Safety Check
        if (empty($objectIds)) {
            return;
        }
        $commentStr = sprintf("%s %s - %s", static::$commentPrefix, implode(", ", $objectIds), ucfirst($action));
        //====================================================================//
        //  Execute Commit
        $connector->commit($objectType, $objectIds, $action, static::$username, $commentStr);
    }
}
