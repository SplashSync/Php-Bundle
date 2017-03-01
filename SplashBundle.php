<?php

namespace Splash\Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Splash\Client\Splash;

class SplashBundle extends Bundle
{
    
    /**
     * @abstract    Boots the Bundle. 
     */
    public function boot()
    {
        //====================================================================//
        // Boot Local Splash Module
        Splash::Local()->Boot($this->container);  
    }
   
}
