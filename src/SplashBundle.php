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

namespace Splash\Bundle;

use Splash\Bundle\Services\ConnectorsManager;
use Splash\Core\Client\Splash;
use Splash\Local\Local;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

/**
 * Splash Bundle for Symfony
 *
 * @property null|ContainerInterface $container
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
        if (isset($this->container)) {
            Assert::isInstanceOf(
                $connectorsManager = $this->container->get("splash.connectors.manager"),
                ConnectorsManager::class
            );
            Assert::isInstanceOf(
                $router = $this->container->get("router"),
                RouterInterface::class
            );

            $local->boot($connectorsManager, $router);
        }
    }
}
