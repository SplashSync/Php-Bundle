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

namespace Splash\Bundle\Phpunit;

use Exception;
use PHPUnit\Framework\Assert;
use Splash\Bundle\Models\AbstractConnector;
use Splash\Bundle\Services\ConnectorsManager;
use Splash\Core\Client\Splash;
use Splash\Local\Local;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouterInterface as Router;

/**
 * Class SymfonyBridge
 *
 * This class serves as a testing utility within a Symfony application, handling
 * the test client, router, connector management, and configuration for server tests.
 */
class SymfonyBridge extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    protected static KernelBrowser $client;

    /**
     * @var Router
     */
    protected static Router $router;

    /**
     * Boot Symfony & Setup First Server Connector For Testing
     */
    public static function onTestSetUp(): void
    {
        //====================================================================//
        // Boot Symfony Kernel
        self::getTestClient();
        $manager = self::getConnectorsManager();

        //====================================================================//
        // Boot Local Splash Module
        /** @var Local $local */
        $local = Splash::local();
        $local->boot($manager, self::getRouter());

        //====================================================================//
        // Check if Server Already Selected
        //====================================================================//
        if (!empty($local->getServerId())) {
            return;
        }

        //====================================================================//
        // Init Local Class with First Server Infos
        //====================================================================//

        //====================================================================//
        // Load Servers Names
        $servers = $manager->getServersNames();
        assert(!empty($servers), "No server Configured for Splash");
        $serverIds = array_keys($servers);
        $local->setServerId((string) array_shift($serverIds));
    }

    /**
     * Execute PhpUnit Test Tear Down Actions from Static Events
     */
    public static function onTestTearDown(): void
    {
        self::getInstance()->tearDown();
    }

    /**
     * Get Framework Test Kernel Browser.
     *
     * @return KernelBrowser
     */
    public static function getTestClient() : KernelBrowser
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
    public static function getRouter() : Router
    {
        //====================================================================//
        // Link to Symfony Router
        if (!isset(static::$router)) {
            /** @var object $router */
            $router = self::getContainer()->get("router");
            Assert::assertInstanceOf(
                Router::class,
                $router,
                'Unable to Load Connectors Manager'
            );

            static::$router = $router;
        }

        return static::$router;
    }

    /**
     * Get Connectors Manager
     */
    protected static function getConnectorsManager() : ConnectorsManager
    {
        try {
            $manager = self::getContainer()->get(ConnectorsManager::class);
        } catch (Exception) {
            $manager = null;
        }
        Assert::assertInstanceOf(
            ConnectorsManager::class,
            $manager,
            'Unable to Load Connectors Manager'
        );

        return $manager;
    }

    /**
     * Get Connector by Server ID For Testing
     *
     * @param string $serverId
     *
     * @return AbstractConnector
     */
    protected static function getConnector(string $serverId) : AbstractConnector
    {
        $connector = self::getConnectorsManager()->get($serverId);
        Assert::assertInstanceOf(
            AbstractConnector::class,
            $connector,
            sprintf('Unable to Load Connector: %s', $serverId)
        );

        return $connector;
    }

    /**
     * Get an Instance of Symfony Bridge
     */
    private static function getInstance(): self
    {
        static $instance = null;

        return $instance ??= new self();
    }
}
