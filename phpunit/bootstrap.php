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

use Splash\Bundle\Phpunit\SymfonyBridge;
use Splash\Tests\WsObjects\C001ObjectsGetMultiTest;
use Splash\Validator\Configuration;

/**
 * Configure Splash Phpunit Framework
 * - Add Symfony Phpunit Bridge SetUp & TearDown Events
 * - Add Connectors Specific Phpunit Tests
 */
if (class_exists(Configuration::class)) {
    Configuration::registerSetUpListener(
        array(SymfonyBridge::class, 'onTestSetUp')
    );
    Configuration::registerTearDownListener(
        array(SymfonyBridge::class, 'onTestTearDown')
    );
    Configuration::registerObjectTestClass(
        C001ObjectsGetMultiTest::class,
    );
}
