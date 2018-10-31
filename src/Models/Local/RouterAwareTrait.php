<?php

/**
 * This file is part of SplashSync Project.
 *
 * Copyright (C) Splash Sync <www.splashsync.com>
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Bernard Paquier <contact@splashsync.com>
 */

namespace Splash\Bundle\Models\Local;

use Symfony\Component\Routing\RouterInterface;

/**
 * @abstract    Make Class Symfony Router Aware
 */
trait RouterAwareTrait
{
    /**
     * @abstract    Symfony Router
     * @var RouterInterface
     */
    private $Router;
    
    /**
     * @abstract    Setup Symfony Router
     * @param       RouterInterface $Router
     * @return      $this
     */
    public function setRouter(RouterInterface $Router)
    {
        $this->Router    =   $Router;
        return $this;
    }
    
    /**
     * @abstract    Get Webservice Host
     * @return  string|null
     */
    public function getServerPath()
    {
        return $this->Router->generate("splash_main_soap");
    }
}
