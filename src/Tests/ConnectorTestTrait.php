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
use Splash\Bundle\Services\ConnectorsManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Routing\RouterInterface as Router;

/**
 * Collection of Helpers for Connectors PhpUnit Tests
 */
trait ConnectorTestTrait
{
    /**
     * @var KernelBrowser
     */
    protected static KernelBrowser $client;

    /**
     * @var Router
     */
    private Router $router;

    /**
     * Get Framework Test Kernel Browser.
     *
     * @return KernelBrowser
     */
    protected function getTestClient() : KernelBrowser
    {
        //====================================================================//
        // Link to Symfony Client
        if (!static::$booted) {
            static::$client = static::createClient();
        }

        return static::$client;
    }

    /**
     * Get Framework Router.
     *
     * @return Router
     */
    protected function getRouter() : Router
    {
        //====================================================================//
        // Link to Symfony Router
        if (!isset($this->router)) {
            $this->router = $this->getContainer()->get("router");
        }

        return $this->router;
    }

    /**
     * Get Connectors Manager
     *
     * @throws Exception
     *
     * @return ConnectorsManager
     */
    protected function getConnectorsManager() : ConnectorsManager
    {
        return $this->getContainer()->get(ConnectorsManager::class);
    }

    /**
     * Get Connector by Server Id For Testing
     *
     * @param string $serverId
     *
     * @throws Exception
     *
     * @return AbstractConnector
     */
    protected function getConnector(string $serverId) : AbstractConnector
    {
        $connector = $this->getConnectorsManager()->get($serverId);
        if (!($connector instanceof AbstractConnector)) {
            throw new Exception(sprintf('Unable to Load Connector: %s', $serverId));
        }

        return $connector;
    }
}
