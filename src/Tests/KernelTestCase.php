<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2020 Splash Sync  <www.splashsync.com>
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
use Splash\Core\SplashCore as Splash;
use Splash\Local\Local;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Throwable;

/**
 * Base PhpUnit Test Class for Splash Modules Tests
 *
 * May be overridden for Using Splash Core Test in Specific Environnements
 */
class TestCase extends BaseTestCase
{
    use \Splash\Bundle\Tests\ConnectorAssertTrait;
    use \Splash\Bundle\Tests\ConnectorTestTrait;
    use \Splash\Tests\Tools\Traits\ObjectsAssertionsTrait;

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
        // Boot Symfony Kernel
        /** @var ContainerInterface $container */
        $container = static::bootKernel()->getContainer();
        //====================================================================//
        // Boot Local Splash Module
        /** @var Local $local */
        $local = Splash::local();
        $local->boot(
            $container->get("splash.connectors.manager"),
            $container->get("router")
        );

        //====================================================================//
        // Init Local Class with First Server Infos
        //====================================================================//

        //====================================================================//
        // Load Servers Namess
        $servers = $container->get("splash.connectors.manager")->getServersNames();
        if (empty($servers)) {
            throw new Exception("No server Configured for Splash");
        }
        $serverIds = array_keys($servers);
        $local->setServerId((string) array_shift($serverIds));

        //====================================================================//
        // Reboot Splash Core Module
        Splash::reboot();
    }

    /**
     * @param Throwable $exception
     *
     * @throws Throwable
     *
     * @return void
     */
    public function onNotSuccessfulTest(Throwable $exception): void
    {
        //====================================================================//
        // Do not display log on Skipped Tests
        if (is_a($exception, "PHPUnit\\Framework\\SkippedTestError")) {
            throw $exception;
        }
        //====================================================================//
        // Remove Debug From Splash Logs
        \Splash\Client\Splash::log()->deb = array();
        //====================================================================//
        // OutPut Splash Logs
        fwrite(STDOUT, Splash::log()->getConsoleLog());
        //====================================================================//
        // OutPut Phpunit Exception
        throw $exception;
    }

    //====================================================================//
    // CORE : SYMFONY CONTAINER FROM KERNEL
    //====================================================================//

    /**
     * Safe Gets the current container.
     *
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        $container = static::$kernel->getContainer();
        if (!($container instanceof ContainerInterface)) {
            throw new Exception('Unable to Load Container');
        }

        return $container;
    }
}
