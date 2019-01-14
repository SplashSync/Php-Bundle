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

namespace Splash\Bundle\Tests;

use Splash\Bundle\Models\AbstractConnector;
use Splash\Client\Splash;
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
        /** @var ContainerInterface $container */
        $container     =   static::$kernel->getContainer();
        if (is_null($container)) {
            throw new Exception('Unable to Load Container');
        }
        
        $connector = $container->get("splash.connectors.manager")->get($serverId);
        if (is_null($connector)) {
            throw new Exception('Unable to Load Connector');
        }

        return $connector;
    }
}
