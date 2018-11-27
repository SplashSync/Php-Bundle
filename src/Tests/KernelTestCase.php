<?php

/*
 * Copyright (C) 2011-2018  Splash Sync       <contact@splashsync.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

namespace Splash\Tests\Tools;

use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase as BaseTestCase;

use Splash\Local\Local;

use Splash\Core\SplashCore as Splash;

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
