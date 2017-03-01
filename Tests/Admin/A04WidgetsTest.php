<?php

namespace Splash\Bundle\Tests\Admin;

use Splash\Bundle\Tests\KernelTestCaseTrait;

/**
 * @abstract    Symfony Admin Test Suite - Ping Client Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class A04WidgetsTest extends  \Splash\Tests\Admin\A04WidgetsTest { 
    
    use KernelTestCaseTrait;
    
    protected function setUp()
    {
        $this->markTestSkipped(
            'Widgets Feature is not available.'
        );
    }    
    
}
