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
use Splash\Bundle\Phpunit\ConnectorTestCase;

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
        Assert::assertNotEmpty($this->getContainer());
    }

    /**
     * Test Loading Kernel Browser Client
     */
    public function testLoadingKernelBrowser(): void
    {
        Assert::assertNotEmpty($this->getTestClient());
    }

    /**
     * Test Loading Symfony Router
     */
    public function testLoadingRouter(): void
    {
        Assert::assertNotEmpty($this->getRouter());
    }

    /**
     * Test Loading Splash Connector Manager
     */
    public function testLoadingManager(): void
    {
        Assert::assertNotEmpty($this->getConnectorsManager());
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
        $this->assertNotEmpty($this->getConnector("node1"));
        $this->assertNotEmpty($this->getConnector("node2"));
    }
}
