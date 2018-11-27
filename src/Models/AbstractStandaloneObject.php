<?php
/**
 * This file is part of SplashSync Project.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 *  @author    Splash Sync <www.splashsync.com>
 *
 *  @copyright 2015-2017 Splash Sync
 *
 *  @license   GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 *
 **/

namespace   Splash\Bundle\Models;

use Splash\Models\AbstractObject;

use Splash\Bundle\Interfaces\Connectors\ConfigurationInterface;
use Splash\Bundle\Models\Connectors\ConfigurationAwareTrait;

/**
 * @abstract    Base Class for Standalone Connector Objects Services
 */
abstract class AbstractStandaloneObject extends AbstractObject implements ConfigurationInterface
{
    use ConfigurationAwareTrait;
}
