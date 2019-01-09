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

namespace Splash\Tests\Tools;

use Exception;
use Splash\Core\SplashCore as Splash;
use Splash\Local\Local;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase as BaseTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @abstract    Base PhpUnit Test Class for Splash Modules Tests
 *              May be overriden for Using Splash Core Test in Specific Environements
 */
class TestCase extends BaseTestCase
{
    use \Splash\Tests\Tools\Traits\SuccessfulTestPHP7;

    /**
     * @abstract    Boot Symfony & Setup First Server Connector For Testing
     *
     * @throws Exception
     */
    protected function setUp()
    {
        //====================================================================//
        // Boot Symfony Kernel
        /** @var ContainerInterface $container */
        $container     =   static::bootKernel()->getContainer();
        //====================================================================//
        // Boot Local Splash Module
        /** @var Local $local */
        $local  =   Splash::local();
        $local->boot(
            $container->get("splash.connectors.manager"),
            $container->get("router")
        );
        
        //====================================================================//
        // Init Local Class with First Server Infos
        //====================================================================//
        
        //====================================================================//
        // Load Servers Namess
        $servers    =   $container->get("splash.connectors.manager")->getServersNames();
        if (empty($servers)) {
            throw new Exception("No server Configured for Splash");
        }
        $serverIds    =   array_keys($servers);
        $local->setServerId(array_shift($serverIds));
        
        //====================================================================//
        // Reboot Splash Core Module
        Splash::reboot();
    }
}
