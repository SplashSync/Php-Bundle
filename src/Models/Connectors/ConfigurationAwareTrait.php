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

namespace Splash\Bundle\Models\Connectors;

/**
 * Manage Connectors Configuration
 */
trait ConfigurationAwareTrait
{
    /**
     * Object or Widget Type Name
     *
     * @var string
     */
    private string $type;

    /**
     * Webservice ID for Connector
     *
     * @var string
     */
    private string $webserviceId;

    /**
     * Connector Configuration
     *
     * @var array
     */
    private array $config;

    /**
     * {@inheritdoc}
     */
    public function configure(string $type, string $webserviceId, array $configuration): self
    {
        $this->type = $type;
        $this->webserviceId = $webserviceId;
        $this->config = $configuration;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isConfigured(): bool
    {
        return !empty($this->webserviceId) && !empty($this->config);
    }

    /**
     * {@inheritdoc}
     */
    public function getSplashType() : string
    {
        return $this->type;
    }

    /**
     * Get Webservice ID
     *
     * @return string
     */
    public function getWebserviceId() : string
    {
        return $this->webserviceId;
    }

    /**
     * Get Connector Configuration
     *
     * @return array
     */
    public function getConfiguration() : array
    {
        return $this->config;
    }

    /**
     * Safe Get of A Global Parameter
     *
     * @param string      $key     Global Parameter Key
     * @param mixed       $default Default Parameter Value
     * @param null|string $domain  Parameters Domain Key
     *
     * @return mixed
     */
    public function getParameter(string $key, $default = null, string $domain = null)
    {
        if ($domain) {
            return $this->config[$domain][$key] ?? $default;
        }

        return $this->config[$key] ?? $default;
    }

    /**
     * Safe Set of A Global Parameter
     *
     * @param string      $key    Global Parameter Key
     * @param mixed       $value  Parameter Value
     * @param null|string $domain Parameters Domain Key
     *
     * @return self
     */
    public function setParameter(string $key, $value, string $domain = null): self
    {
        if (is_null($domain)) {
            $this->config[$key] = $value;

            return $this;
        }
        if (!isset($this->config[$domain])) {
            $this->config[$domain] = array();
        }
        $this->config[$domain][$key] = $value;

        return $this;
    }

    /**
     * Setup Connector Type
     *
     * @param string $type
     *
     * @return self
     */
    protected function setSplashType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
