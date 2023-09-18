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
use Splash\Bundle\Services\ConnectorsManager;
use Splash\Core\SplashCore as Splash;
use Splash\Local\Local;
use Splash\Tests\Tools\Traits\ObjectsAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Throwable;

/**
 * Base PhpUnit Test Class for Splash Modules Tests
 *
 * May be overridden for Using Splash Core Test in Specific Environnements
 */
class WebTestCase extends BaseTestCase
{
    use ConnectorAssertTrait;
    use ConnectorTestTrait;
    use ObjectsAssertionsTrait;

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
        $this->getTestClient();
        $manager = $this->getContainer()->get(ConnectorsManager::class);
        //====================================================================//
        // Boot Local Splash Module
        /** @var Local $local */
        $local = Splash::local();
        $local->boot($manager, $this->getRouter());

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
