<?php

namespace Splash\Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Splash\Client\Splash;
use Splash\Local\Local;

class SplashBundle extends Bundle
{
    
    /**
     * @abstract    Boots the Bundle.
     */
    public function boot()
    {
        //====================================================================//
        // Push Symfony Service Container to Local Splash Module
        Local::setContainer($this->container);
        //====================================================================//
        // Init Local Splash Module for Loading Dependencies
        Splash::Core();
    }
}
