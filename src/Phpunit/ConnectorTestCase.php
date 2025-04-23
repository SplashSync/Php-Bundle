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
use Splash\Bundle\Phpunit\Providers\ConnectorsProviderTrait;
use Splash\Core\Client\Splash;
use Splash\Core\Components\StaticEventsManager;
use Splash\Validator\Phpunit\Providers\ObjectsProviderTrait;
use Splash\Validator\Phpunit\Providers\SequencesProviderTrait;
use Splash\Validator\Phpunit\Providers\WidgetsProviderTrait;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Throwable;
use Splash\Validator\Phpunit\Events;

/**
 * Base PhpUnit Test Class for Splash Connectors Tests
 */
abstract class ConnectorTestCase extends SymfonyBridge
{
    use SequencesProviderTrait;
    use ObjectsProviderTrait;
    use WidgetsProviderTrait;
    use ConnectorsProviderTrait;

    //====================================================================//
    // PhpUnit Test Case Method Wrapping
    //====================================================================//

    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass(): void
    {
        //====================================================================//
        // Ensure Server Mode is Active
        if (!defined("SPLASH_SERVER_MODE")) {
            define("SPLASH_SERVER_MODE", true);
        }
        //====================================================================//
        // Execute System Events
        StaticEventsManager::execute(new Events\SetUpBeforeClassEvent());

        parent::setUpBeforeClass();
    }

    /**
     * This method is called after the last test of this test class is run.
     */
    public static function tearDownAfterClass(): void
    {
        //====================================================================//
        // Execute System Events
        StaticEventsManager::execute(new Events\TearDownAfterClassEvent());

        parent::tearDownAfterClass();
    }

    /**
     * {@inheritDoc}
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
        // Execute System Events
        StaticEventsManager::execute(new Events\SetUpEvent());

        parent::setUp();

        //====================================================================//
        // BOOT or REBOOT MODULE
        Splash::reboot();
    }

    /**
     * This method is called after each test.
     */
    protected function tearDown(): void
    {
        //====================================================================//
        // Execute System Events
        StaticEventsManager::execute(new Events\TearDownEvent());

        parent::tearDown();
    }

    /**
     * {@inheritDoc}
     */
    public function onNotSuccessfulTest(Throwable $throwable): void
    {
        //====================================================================//
        // Execute System Events
        StaticEventsManager::execute(new Events\NonSuccessfulTestEvent($throwable));

        //====================================================================//
        // OutPut Phpunit Exception
        throw $throwable;
    }
}
