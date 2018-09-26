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


namespace Splash\Bundle\Services;

use Splash\Bundle\Models\Connectors\ConnectorInterface;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @abstract Splash Bundle Connectors Manager
 */
class ConnectorsManager {

    /**
     * Splash Connectors Configuration Array
     * @var array
     */
    private $Config;
    
    /**
     * @var     EventDispatcherInterface
     */
    private  $Dispatcher;
    
    public function __construct(array $Config, EventDispatcherInterface $Dispatcher) {
        $this->Dispatcher      =   $Dispatcher;
    }      

}
