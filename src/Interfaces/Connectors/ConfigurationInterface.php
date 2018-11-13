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

namespace Splash\Bundle\Interfaces\Connectors;

/**
 * @abstract Define Required structure for Connectors Configuration
 */
interface ConfigurationInterface
{
    /**
     * @abstract    Set Connector Configuration
     * @param   array $Configuration
     * @return  $this
     */
    public function configure(string $WebserviceId, array $Configuration);
    
    /**
     * @abstract       Safe Get of A Global Parameter
     *
     * @param      string   $Key        Global Parameter Key
     * @param      mixed    $Default    Default Parameter Value
     * @param      string   $Domain     Parameters Domain Key
     *
     * @return     mixed
     */
    public function getParameter($Key, $Default = null, $Domain = null);
    
    /**
     * @abstract       Safe Set of A Global Parameter
     *
     * @param      string  $Key         Global Parameter Key
     * @param      mixed   $Value       Parameter Value
     * @param      string  $Domain      Parameters Domain Key
     *
     * @return     self
     */
    public function setParameter($Key, $Value, $Domain = null);
    
}
