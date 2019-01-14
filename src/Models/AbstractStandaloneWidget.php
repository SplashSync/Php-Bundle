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

namespace   Splash\Bundle\Models;

use Splash\Bundle\Interfaces\Connectors\ConfigurationInterface;
use Splash\Bundle\Models\Connectors\ConfigurationAwareTrait;
use Splash\Models\AbstractWidget;

/**
 * Base Class for Standalone Connector Widget Services
 */
abstract class AbstractStandaloneWidget extends AbstractWidget implements ConfigurationInterface
{
    use ConfigurationAwareTrait;
}
