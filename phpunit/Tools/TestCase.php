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

namespace Splash\Tests\Tools;

use Exception;
use Splash\Bundle\Services\ConnectorsManager;
use Splash\Bundle\Tests as BundleTraits;
use Splash\Core\SplashCore as Splash;
use Splash\Local\Local;
use Splash\Tests\Tools\Traits as CoreTraits;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base PhpUnit Test Class for Splash Modules Tests
 *
 * May be overridden for Using Splash Core Test in Specific Environnements
 */
abstract class TestCase extends WebTestCase
{
    use CoreTraits\ObjectsAssertionsTrait;
    use CoreTraits\ConsoleLogTrait;
    use CoreTraits\InitializationTrait;
    use BundleTraits\ConnectorAssertTrait;
    use BundleTraits\ConnectorTestTrait;

    /**
     * Boot Symfony & Setup First Server Connector For Testing
     *
     * @throws Exception
     *
     * @return void
     */
    protected function setUp(): void
    {
        //====================================================================//
        // Safety Check
        if (!class_exists(AbstractBrowser::class)) {
            throw new Exception(sprintf(
                'Class "%s()" not found, try require symfony/browser-kit.',
                AbstractBrowser::class
            ));
        }
        //====================================================================//
        // Boot Symfony Kernel
        $this->getTestClient();
        $manager = $this->getContainer()->get(ConnectorsManager::class);
        //====================================================================//
        // Boot Local Splash Module
        /** @var Local $local */
        $local = Splash::local();
        $local->boot($manager, $this->getRouter());

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
        if (empty($servers)) {
            throw new Exception("No server Configured for Splash");
        }
        $serverIds = array_keys($servers);
        $local->setServerId((string) array_shift($serverIds));

        //====================================================================//
        // Reboot Splash Core Module
        Splash::reboot();
    }

    //====================================================================//
    // CORE : SYMFONY CONTAINER FROM KERNEL
    //====================================================================//

    /**
     * Safe Gets the current container.
     *
     * @throws Exception
     *
     * @return ContainerInterface
     */
    protected static function getContainer(): ContainerInterface
    {
        $container = static::$kernel->getContainer();
        if (!($container instanceof ContainerInterface)) {
            throw new Exception('Unable to Load Container');
        }

        return $container;
    }
}
