<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2019 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Models\Local;

use Symfony\Component\Routing\RouterInterface;

/**
 * @abstract    Make Class Symfony Router Aware
 */
trait RouterAwareTrait
{
    /**
     * Symfony Router
     *
     * @var RouterInterface
     */
    private $router;

    /**
     * Setup Symfony Router
     *
     * @param RouterInterface $router
     *
     * @return self
     */
    public function setRouter(RouterInterface $router): self
    {
        $this->router = $router;

        return $this;
    }

    /**
     * Get Webservice Host
     *
     * @return null|string
     */
    public function getServerPath()
    {
        return $this->router->generate("splash_main_soap");
    }
}
