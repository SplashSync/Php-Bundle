<?php

namespace Splash\Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Splash\Client\Splash;
use Splash\Local\Local;

/**
 * @abstract    Splash Bundle for Symfony
 */
class SplashBundle extends Bundle
{

    /**
     * @abstract    Boots the Bundle.
     */
    public function boot()
    {
        /** @var Local $local */
        $local  =   Splash::local();
        //====================================================================//
        // Boot Local Splash Module
        $local->boot($this->container->get("splash.connectors.manager"), $this->container->get("router"));
    }
}
