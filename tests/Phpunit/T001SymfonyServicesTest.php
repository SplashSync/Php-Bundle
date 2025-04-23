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

namespace Splash\Bundle\Tests\Phpunit;

use Exception;
use PHPUnit\Framework\Assert;
use Splash\Bundle\Models\AbstractConnector;
use Splash\Bundle\Phpunit\ConnectorTestCase;
use Splash\Bundle\Services\ConnectorsManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Test Loading of Symfony Services from Test Case
 */
class T001SymfonyServicesTest extends ConnectorTestCase
{
    /**
     * Test Loading Symfony Container
     */
    public function testLoadingContainer(): void
    {
        Assert::assertInstanceOf(ContainerInterface::class, $this->getContainer());
    }

    /**
     * Test Loading Kernel Browser Client
     */
    public function testLoadingKernelBrowser(): void
    {
        Assert::assertInstanceOf(KernelBrowser::class, $this->getTestClient());
    }


    /**
     * Test Loading Symfony Router
     */
    public function testLoadingRouter(): void
    {
        Assert::assertInstanceOf(RouterInterface::class, $this->getRouter());
    }

    /**
     * Test Loading Splash Connector Manager
     */
    public function testLoadingManager(): void
    {
        Assert::assertInstanceOf(ConnectorsManager::class, $this->getConnectorsManager());
    }

    /**
     * Test Loading Kernel Browser Client
     *
     * @throws Exception
     *
     * @return void
     */
    public function testLoadingConnector(): void
    {
        $this->assertInstanceOf(AbstractConnector::class, $this->getConnector("node1"));
        $this->assertInstanceOf(AbstractConnector::class, $this->getConnector("node2"));
    }
}
