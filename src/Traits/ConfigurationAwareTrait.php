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

namespace Splash\Bundle\Traits;

/**
 * Configuration Aware Trait
 */
trait ConfigurationAwareTrait {

    /**
     * @abstract    Connector Configuration
     * @var array
     */
    private $config;


    /**
     * @abstract    Set Connector Configuration
     * @param   array $Configuration
     * @return  $this
     */
    public function setConfiguration(array $Configuration) {
        $this->config   =   $Configuration;
        return $this;
    }

    /**
     * @abstract    Get Connector Configuration
     * @return  array
     */
    public function getConfiguration() {
        return $this->config;
    }
    
    /**
     * @abstract       Safe Get of A Global Parameter
     *
     * @param      string  $Key      Global Parameter Key
     * @param      string  $Default  Default Parameter Value
     * @param      string  $Domain   Parameters Domain Key
     *
     * @return     string
     */
    protected function getParameter($Key, $Default = null, $Domain = null)
    {
        if ($Domain) {
            return isset($this->config[$Domain][$Key])  ? $this->config[$Domain][$Key] : $Default;
        }
        return isset($this->config[$Key])  ? $this->config[$Key] : $Default;
    }    
    
}
