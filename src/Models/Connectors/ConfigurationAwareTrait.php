<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2018 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Models\Connectors;

/**
 * @abstract    Manage Connectors Configuration
 */
trait ConfigurationAwareTrait
{
    /**
     * @abstract    Webservice Id for Connector
     *
     * @var string
     */
    private $webserviceId;
    
    /**
     * @abstract    Connector Configuration
     *
     * @var array
     */
    private $config;

    /**
     * {@inheritdoc}
     */
    public function configure(string $webserviceId, array $configuration)
    {
        $this->webserviceId =   $webserviceId;
        $this->config       =   $configuration;

        return $this;
    }

    /**
     * @abstract    Get Webservice Id
     *
     * @return  string
     */
    public function getWebserviceId() : string
    {
        return $this->webserviceId;
    }
    
    /**
     * @abstract    Get Connector Configuration
     *
     * @return  array
     */
    public function getConfiguration() : array
    {
        return $this->config;
    }
    
    /**
     * @abstract       Safe Get of A Global Parameter
     *
     * @param      string $key     Global Parameter Key
     * @param      mixed  $default Default Parameter Value
     * @param      string $domain  Parameters Domain Key
     *
     * @return     mixed
     */
    public function getParameter($key, $default = null, $domain = null)
    {
        if ($domain) {
            return isset($this->config[$domain][$key])  ? $this->config[$domain][$key] : $default;
        }

        return isset($this->config[$key])  ? $this->config[$key] : $default;
    }
    
    /**
     * @abstract       Safe Set of A Global Parameter
     *
     * @param      string $key    Global Parameter Key
     * @param      mixed  $value  Parameter Value
     * @param      string $domain Parameters Domain Key
     *
     * @return     self
     */
    public function setParameter($key, $value, $domain = null)
    {
        if (is_null($domain)) {
            $this->config[$key]      =    $value;

            return $this;
        }
        if (!isset($this->config[$domain])) {
            $this->config[$domain]      =    array();
        }
        $this->config[$domain][$key]    =   $value;

        return $this;
    }
}
