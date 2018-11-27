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
     *
     * @param   array $configuration
     *
     * @return  $this
     */
    public function configure(string $webserviceId, array $configuration);
    
    /**
     * @abstract       Safe Get of A Global Parameter
     *
     * @param      string $key     Global Parameter Key
     * @param      mixed  $default Default Parameter Value
     * @param      string $domain  Parameters Domain Key
     *
     * @return     mixed
     */
    public function getParameter($key, $default = null, $domain = null);
    
    /**
     * @abstract       Safe Set of A Global Parameter
     *
     * @param      string $key    Global Parameter Key
     * @param      mixed  $value  Parameter Value
     * @param      string $domain Parameters Domain Key
     *
     * @return     self
     */
    public function setParameter($key, $value, $domain = null);
}
