<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Local;

// From Splash PhpCore
use Splash\Bundle\Models\Local\ConnectorsManagerAwareTrait;
use Splash\Bundle\Models\Local\CoreTrait;
// From Splash Bundle
use Splash\Bundle\Models\Local\ObjectsTrait;
use Splash\Bundle\Models\Local\RouterAwareTrait;
use Splash\Bundle\Models\Local\TestTrait;
use Splash\Bundle\Models\Local\WidgetsTrait;
use Splash\Bundle\Services\ConnectorsManager;
use Splash\Models\LocalClassInterface;
// From Symfony
use Splash\Models\ObjectsProviderInterface;
use Splash\Models\WidgetsProviderInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Splash Bundle Local Server Class
 */
class Local implements LocalClassInterface, ObjectsProviderInterface, WidgetsProviderInterface
{
    use ConnectorsManagerAwareTrait;
    use RouterAwareTrait;
    use CoreTrait;
    use TestTrait;
    use ObjectsTrait;
    use WidgetsTrait;

    /**
     * Boots the Bundle
     *
     * @param ConnectorsManager $manager
     * @param RouterInterface   $router
     *
     * @return self
     */
    public function boot(ConnectorsManager $manager, RouterInterface $router): self
    {
        return $this->setManager($manager)->setRouter($router);
    }
}
