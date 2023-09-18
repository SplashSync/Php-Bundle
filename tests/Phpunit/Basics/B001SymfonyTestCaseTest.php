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

namespace Splash\Bundle\Tests\Phpunit\Basics;

use Exception;
use Splash\Bundle\Models\AbstractConnector;
use Splash\Bundle\Tests\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DependencyInjection\ContainerInterface;

class B001SymfonyTestCaseTest extends WebTestCase
{
    /**
     * Test Loading Symfony Container
     *
     * @throws Exception
     *
     * @return void
     */
    public function testSplashDefinitionsExists(): void
    {
        $this->assertTrue(defined("SPL_PROTOCOL"));
        $this->assertTrue(defined("SPL_T_VARCHAR"));
        $this->assertEquals("varchar", SPL_T_VARCHAR);
    }

    /**
     * Test Loading Symfony Container
     *
     * @throws Exception
     *
     * @return void
     */
    public function testLoadingContainer(): void
    {
        $this->assertInstanceOf(ContainerInterface::class, $this->getContainer());
    }

    /**
     * Test Loading Kernel Browser Client
     *
     * @throws Exception
     *
     * @return void
     */
    public function testLoadingKernelBrowser(): void
    {
        $this->assertInstanceOf(KernelBrowser::class, $this->getTestClient());
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
