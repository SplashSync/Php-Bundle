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

namespace Splash\Bundle\Interfaces\Connectors;

/**
 * Define Required structure for Connectors Configuration
 */
interface ConfigurationInterface
{
    /**
     * Set Connector Configuration
     *
     * @param string $type
     * @param string $webserviceId
     * @param array  $configuration
     *
     * @return $this
     */
    public function configure(string $type, string $webserviceId, array $configuration): self;

    /**
     * Check if Connector is Fully Configured
     *
     * @return bool
     */
    public function isConfigured() : bool;

    /**
     * Get Connector | Object | Widget Type Name
     *
     * @return string
     */
    public function getSplashType() : string;

    /**
     * Safe Get of A Global Parameter
     *
     * @param string      $key     Global Parameter Key
     * @param mixed       $default Default Parameter Value
     * @param null|string $domain  Parameters Domain Key
     *
     * @return mixed
     */
    public function getParameter(string $key, $default = null, string $domain = null);

    /**
     * Safe Set of A Global Parameter
     *
     * @param string      $key    Global Parameter Key
     * @param mixed       $value  Parameter Value
     * @param null|string $domain Parameters Domain Key
     *
     * @return self
     */
    public function setParameter(string $key, $value, string $domain = null): self;
}
