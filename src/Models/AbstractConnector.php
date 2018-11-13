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
 **/

namespace Splash\Bundle\Models;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Splash\Bundle\Interfaces\ConnectorInterface;

use Splash\Bundle\Models\Connectors\ConfigurationAwareTrait;
use Splash\Bundle\Models\Connectors\EventDispatcherAwareTrait;

use Splash\Bundle\Events\UpdateConfigurationEvent;

/**
 * @abstract Base Class for All Splash Bundle Connectors
 */
abstract class AbstractConnector implements ConnectorInterface
{

    use ConfigurationAwareTrait;
    use EventDispatcherAwareTrait;
            
    public function __construct(EventDispatcherInterface $EventDispatcher)
    {
        $this->setEventDispatcher($EventDispatcher);
    }
    
    /**
     * Ask for Update of Server Configuration in Memory
     */
    public function updateConfiguration()
    {      
        $this->getEventDispatcher()->dispatch(
                UpdateConfigurationEvent::NAME,
                new UpdateConfigurationEvent($this->getWebserviceId(), $this->getConfiguration())
            );
    }      
}
