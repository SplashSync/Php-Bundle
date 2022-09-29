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

namespace Splash\Local\Objects;

use Splash\Bundle\Interfaces\ConnectorInterface;
use Splash\Bundle\Interfaces\Connectors\PrimaryKeysInterface;
use Splash\Models\Objects\LockTrait;
use Splash\Models\Objects\ObjectInterface;
use Splash\Models\Objects\PrimaryKeysAwareInterface;

/**
 * Splash Bundle Connectors Objects Access
 */
class Manager implements ObjectInterface, PrimaryKeysAwareInterface
{
    use LockTrait;

    /**
     * Object Name
     *
     * @var string
     */
    protected static string $name = __CLASS__;

    /**
     * @var ConnectorInterface
     */
    private ConnectorInterface $connector;

    /**
     * @var string
     */
    private string $objectType;

    //====================================================================//
    // Class Constructor
    //====================================================================//

    /**
     * Init a New Object Manager
     *
     * @param ConnectorInterface $connector
     * @param string             $objectType
     */
    public function __construct(ConnectorInterface $connector, string $objectType)
    {
        $this->connector = $connector;
        $this->objectType = $objectType;
    }

    //====================================================================//
    //  COMMON CLASS INFORMATION
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    public static function isDisabled(): bool
    {
        return false;
    }

    //====================================================================//
    // Class Main Functions
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    public function description(): array
    {
        //====================================================================//
        // Forward Action
        return $this->connector->getObjectDescription($this->objectType);
    }

    /**
     * {@inheritdoc}
     */
    public function fields(): array
    {
        //====================================================================//
        // Forward Action
        return $this->connector->getObjectFields($this->objectType);
    }

    /**
     * {@inheritdoc}
     */
    public function objectsList(string $filter = null, array $params = array()): array
    {
        //====================================================================//
        // Forward Action
        return $this->connector->getObjectList($this->objectType, $filter, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function getByPrimary(array $keys): ?string
    {
        //====================================================================//
        // Check Connector
        if ($this->connector instanceof PrimaryKeysInterface) {
            //====================================================================//
            // Forward Action
            return $this->connector->getObjectIdByPrimary($this->objectType, $keys);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $objectId, array $fields): ?array
    {
        //====================================================================//
        // Forward Action
        return $this->connector->getObject($this->objectType, $objectId, $fields);
    }

    /**
     * {@inheritdoc}
     */
    public function set(?string $objectId, array $objectData): ?string
    {
        //====================================================================//
        // Forward Action
        return $this->connector->setObject($this->objectType, $objectId, $objectData);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $objectId): bool
    {
        //====================================================================//
        // Forward Action
        return $this->connector->deleteObject($this->objectType, $objectId);
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectIdentifier(): ?string
    {
        return null;
    }
}
