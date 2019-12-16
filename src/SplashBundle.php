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

namespace Splash\Bundle;

use Splash\Client\Splash;
use Splash\Local\Local;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Splash Bundle for Symfony
 */
class SplashBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function boot(): void
    {
        /** @var Local $local */
        $local = Splash::local();
        //====================================================================//
        // Boot Local Splash Module
        $local->boot($this->container->get("splash.connectors.manager"), $this->container->get("router"));
    }
}
