<?php

namespace Splash\Bundle\Phpunit;

use Exception;
use Splash\Bundle\Models\AbstractConnector;
use Splash\Bundle\Services\ConnectorsManager;
use Splash\Core\SplashCore as Splash;
use Splash\Local\Local;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\Routing\RouterInterface as Router;

/**
 * Class SymfonyBridge
 *
 * This class serves as a testing utility within a Symfony application, handling
 * the test client, router, connectors management, and configuration for server tests.
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
    private static Router $router;

    /**
     * Boot Symfony & Setup First Server Connector For Testing
     */
    public static function onTestSetUp(): void
    {
        //====================================================================//
        // Safety Check - Ensure Browser Kit is also installed
        assert(class_exists(AbstractBrowser::class), sprintf(
            'Class "%s()" not found, try require symfony/browser-kit.',
            AbstractBrowser::class
        ));
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
        static::ensureKernelShutdown();
        static::$class = null;
        static::$kernel = null;
        static::$booted = false;
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
    protected static function getRouter() : Router
    {
        //====================================================================//
        // Link to Symfony Router
        if (!isset(static::$router)) {
            static::$router = self::getContainer()->get("router");
        }

        return static::$router;
    }

    /**
     * Get Connectors Manager
     *
     * @return ConnectorsManager
     */
    protected static function getConnectorsManager() : ConnectorsManager
    {
        try {
            $manager = self::getContainer()->get(ConnectorsManager::class);
        } catch (Exception) {
            $manager = null;
        }
        assert(
            $manager instanceof ConnectorsManager,
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
    public static function getConnector(string $serverId) : AbstractConnector
    {
        $connector = self::getConnectorsManager()->get($serverId);
        assert(
            $connector instanceof AbstractConnector,
            sprintf('Unable to Load Connector: %s', $serverId)
        );

        return $connector;
    }
}