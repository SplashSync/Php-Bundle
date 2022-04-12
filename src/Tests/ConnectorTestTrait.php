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

namespace Splash\Bundle\Tests;

use Exception;
use Splash\Bundle\Models\AbstractConnector;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Collection of Helpers for Connectors PhpUnit Tests
 */
trait ConnectorTestTrait
{
    /**
     * Get Connector by Server Id For Testing
     *
     * @param string $serverId
     *
     * @return AbstractConnector
     */
    protected function getConnector(string $serverId) : AbstractConnector
    {
        $container = static::$kernel->getContainer();
        if (!($container instanceof ContainerInterface)) {
            throw new Exception('Unable to Load Container');
        }

        $connector = $container->get("splash.connectors.manager")->get($serverId);
        if (!($connector instanceof AbstractConnector)) {
            throw new Exception('Unable to Load Connector');
        }

        return $connector;
    }
}
