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

namespace Splash\Local;

use Splash\Core\Interfaces\Local\LocalClassInterface;
use Splash\Bundle\Models\Local\ConnectorsManagerAwareTrait;
use Splash\Bundle\Models\Local as Traits;
use Splash\Bundle\Services\ConnectorsManager;
use Splash\Core\Interfaces\FileProviderInterface;
use Splash\Core\Interfaces\Local\ObjectsProviderInterface;
use Splash\Core\Interfaces\Local\WidgetsProviderInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Splash Bundle Local Server Class
 */
class Local implements LocalClassInterface, ObjectsProviderInterface, WidgetsProviderInterface, FileProviderInterface
{
    use ConnectorsManagerAwareTrait;
    use Traits\RouterAwareTrait;
    use Traits\CoreTrait;
    use Traits\TestTrait;
    use Traits\ObjectsTrait;
    use Traits\FilesTrait;
    use Traits\WidgetsTrait;

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
