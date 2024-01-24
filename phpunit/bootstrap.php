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

/**
 * Configure Splash Core for Phpunit in Symfony Environments
 *
 * - Override Splash base test case with Symfony WebTestCase
 */
if (class_exists("PHPUnit\\Framework\\TestCase")) {
    include __DIR__."/Tools/TestCase.php";
}
