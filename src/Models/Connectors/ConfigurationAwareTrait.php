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

namespace Splash\Bundle\Models\Connectors;

/**
 * @abstract    Manage Connectors Configuration
 */
trait ConfigurationAwareTrait
{

    /**
     * @abstract    Webservice Id for Connector
     * @var string
     */
    private $WebserviceId;
    
    /**
     * @abstract    Connector Configuration
     * @var array
     */
    private $Config;

    /**
     * {@inheritdoc}
     */
    public function configure(string $WebserviceId, array $Configuration)
    {
        $this->WebserviceId =   $WebserviceId;
        $this->Config       =   $Configuration;
        return $this;
    }

    /**
     * @abstract    Get Webservice Id
     * @return  string
     */
    public function getWebserviceId() : string
    {
        return $this->WebserviceId;
    }
    
    /**
     * @abstract    Get Connector Configuration
     * @return  array
     */
    public function getConfiguration() : array
    {
        return $this->Config;
    }
    
    /**
     * @abstract       Safe Get of A Global Parameter
     *
     * @param      string  $Key      Global Parameter Key
     * @param      string  $Default  Default Parameter Value
     * @param      string  $Domain   Parameters Domain Key
     *
     * @return     mixed
     */
    public function getParameter($Key, $Default = null, $Domain = null)
    {
        if ($Domain) {
            return isset($this->Config[$Domain][$Key])  ? $this->Config[$Domain][$Key] : $Default;
        }
        return isset($this->Config[$Key])  ? $this->Config[$Key] : $Default;
    }
    
    /**
     * @abstract       Safe Set of A Global Parameter
     *
     * @param      string  $Key         Global Parameter Key
     * @param      mixed   $Value       Parameter Value
     * @param      string  $Domain      Parameters Domain Key
     *
     * @return     self
     */
    public function setParameter($Key, $Value, $Domain = null)
    {
        if (is_null($Domain)) {
            $this->Config[$Key]      =    $Value;
            return $this;
        }
        if (!isset($this->Config[$Domain])) {
            $this->Config[$Domain]      =    array();
        }
        $this->Config[$Domain][$Key]    =   $Value;
        return $this;
    }    
}
