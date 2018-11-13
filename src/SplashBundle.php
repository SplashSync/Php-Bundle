<?php

namespace Splash\Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Splash\Client\Splash;
//use Splash\Local\Local;

class SplashBundle extends Bundle
{
    
    /**
     * @abstract    Boots the Bundle.
     */
    public function boot()
    {
        //====================================================================//
        // Boot Local Splash Module
        Splash::local()->boot($this->container->get("splash.connectors.manager"), $this->container->get("router"));

    }
}
