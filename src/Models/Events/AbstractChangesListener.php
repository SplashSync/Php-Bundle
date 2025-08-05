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

namespace Splash\Bundle\Models\Events;

use Exception;
use Splash\Bundle\Connectors\Standalone;
use Splash\Bundle\Models\AbstractConnector;
use Splash\Bundle\Services\ConnectorsManager;
use Splash\Core\Client\Splash;
use Splash\Core\Dictionary\SplOperations;
use Splash\Local\Local;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

/**
 * Base Class for Splash Objects Changes Listeners
 */
abstract class AbstractChangesListener
{
    use ListenerWithStatesTrait;
    use ListenerWithClassMapTrait;

    /**
     * Username used for Commits
     *
     * @var string
     */
    protected static string $username = "Symfony User";

    //====================================================================//
    //  CONSTRUCTOR
    //====================================================================//

    /**
     * Service Constructor
     */
    public function __construct(
        private readonly ConnectorsManager $connectorsManager
    ) {
        //====================================================================//
        // Safety Check - Ensure Tracked Entities are Given
        Assert::notEmpty(
            static::getClassMap(),
            "No Tracked Objects Class Defined in Class Map."
        );
    }

    //====================================================================//
    //  Methods to Overrides
    //====================================================================//

    /**
     * Get Event Single Objects ID
     *
     * Override this method to define custom Object ID extractors
     *
     * @SuppressWarnings(UnusedFormalParameter)
     */
    protected function getObjectIdentifier(object $object, AbstractConnector $connector): ?string
    {
        $objectId = null;
        //====================================================================//
        // Get Object ID using Getter
        if (method_exists($object, "getId")) {
            $objectId = $object->getId();
        }
        //====================================================================//
        // Get Object ID public Property
        if (property_exists($object, "id")) {
            $objectId = $object->id;
        }
        //====================================================================//
        // Safety Checks
        Assert::notNull($objectId, sprintf(
            "Unable to detect Id of %s object, make sure getId method or id property exists.",
            get_class($object)
        ));
        Assert::scalar($objectId, sprintf(
            "Identifier for %s object must be scalar, %s given.",
            get_class($object),
            gettype($objectId)
        ));

        return (string) $objectId;
    }

    /**
     * Get Username to Display on Splash User Account & Changes Log
     * Override this method to detect the username currently doing action
     *
     * @return string
     */
    protected static function getUsername(): string
    {
        return static::$username;
    }

    /**
     * Get Change Comment to Display on Splash User Account & Changes Log
     * Override this method to change the commit message format
     *
     * @param string[] $objectIds List of Splash Commited Objects IDs
     * @param string   $action    Commited Action Name
     */
    protected static function getComment(array $objectIds, string $action): string
    {
        return sprintf(
            "%s %s - %s",
            "Object",
            implode(", ", $objectIds),
            ucfirst($action)
        );
    }

    /**
     * Get the list of Target Connector Type Names / Codes
     *
     * @return string[]
     */
    protected static function getConnectorNames(): array
    {
        return array(
            Standalone::NAME
        );
    }

    //====================================================================//
    //  Tooling Methods
    //====================================================================//

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
     * Safe Get Event Objects Ids
     * Always returns an array of Object Ids
     *
     * @param GenericEvent      $event
     * @param AbstractConnector $connector
     *
     * @throws Exception
     *
     * @return string[]
     */
    private function getObjectIdentifiers(GenericEvent $event, AbstractConnector $connector): array
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
            // Extract Object ID
            $objectIds[] = $this->getObjectIdentifier($object, $connector);
        }

        return array_unique(array_filter($objectIds));
    }

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
        // Search in Configured Servers using Connector Name
        $servers = array();
        foreach (static::getConnectorNames() as $connectorName) {
            $servers = array_merge(
                $servers,
                $this->connectorsManager->getConnectorConfigurations($connectorName),
            );
        }
        //====================================================================//
        // Walk on Configured Servers
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
        $this->connectorsManager->pushLogToSession(true);
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
        // Load Connector
        $connector = $this->connectorsManager->get($serverId);
        //====================================================================//
        // Safety Check
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
        // Transform Entities to Object Ids
        $objectIds = $this->getObjectIdentifiers($event, $connector);
        //====================================================================//
        // Safety Check
        if (empty($objectIds)) {
            return;
        }
        //====================================================================//
        // Execute Commit from Connector
        $connector->commit(
            $objectType,
            $objectIds,
            $action,
            $this->getUsername(),
            $this->getComment($objectIds, $action),
        );
    }
}
