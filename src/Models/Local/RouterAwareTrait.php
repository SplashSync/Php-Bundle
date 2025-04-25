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

namespace Splash\Bundle\Models\Local;

use Splash\Bundle\Dictionary\SplashBundleRoutes;
use Symfony\Component\Routing\RouterInterface;

/**
 * Make Class Symfony Router Aware
 */
trait RouterAwareTrait
{
    /**
     * Symfony Router
     *
     * @var RouterInterface
     */
    private RouterInterface $router;

    /**
     * Setup Symfony Router
     */
    public function setRouter(RouterInterface $router): self
    {
        $this->router = $router;

        return $this;
    }

    /**
     * Get Webservice Host
     */
    public function getServerPath(): ?string
    {
        return $this->router->generate(SplashBundleRoutes::SOAP);
    }
}
