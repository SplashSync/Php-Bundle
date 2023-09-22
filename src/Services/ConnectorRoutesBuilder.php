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

namespace Splash\Bundle\Services;

use Splash\Bundle\Models\AbstractConnector;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Generate Routes for Connectors with Hostname conversions
 */
class ConnectorRoutesBuilder
{
    /**
     * List of Aliases for Hostnames
     */
    const HOSTS_ALIASES = array(
        "127.0.0.1" => "eu-99.splashsync.com",
        "localhost" => "eu-99.splashsync.com",
        "toolkit.shopify.local" => "eu-99.splashsync.com",
        "eu-99.splashsync.com" => "app-99.splashsync.com",
        "www.splashsync.com" => "proxy.splashsync.com",
        "app.splashsync.com" => "proxy.splashsync.com",
        "admin.splashsync.com" => "proxy.splashsync.com"
    );

    public function __construct(private RouterInterface $router)
    {
    }

    /**
     * Generate Connector Url for Master Action
     */
    public function getMasterActionUrl(AbstractConnector $connector): string
    {
        $this->setupRouter();

        return $this->router->generate(
            'splash_connector_action',
            array(
                'connectorName' => $connector->getProfile()["name"],
                'webserviceId' => $connector->getWebserviceId(),
            ),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * Generate Connector Url for Public Action
     */
    public function getPublicActionUrl(AbstractConnector $connector, string $action): string
    {
        $this->setupRouter();

        return $this->router->generate(
            'splash_connector_action',
            array(
                'connectorName' => $connector->getProfile()["name"],
                'webserviceId' => $connector->getWebserviceId(),
                'action' => $action,
            ),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * Generate Connector Url for Secured Action
     */
    public function getSecuredActionUrl(AbstractConnector $connector, string $action): string
    {
        $this->setupRouter();

        return $this->router->generate(
            'splash_connector_action',
            array(
                'connectorName' => $connector->getProfile()["name"],
                'webserviceId' => $connector->getWebserviceId(),
                'action' => $action,
            ),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * Setup HostName for Router
     */
    private function setupRouter(): void
    {
        $this->router->getContext()
            ->setHost($this->getHostname())
            ->setScheme("https")
            ->setHttpsPort(443)
        ;
    }

    /**
     * Get HostName for Connector Actions
     *
     * @return string
     */
    private function getHostname(): string
    {
        //====================================================================//
        // Get Current Server Name
        $hostName = $this->router->getContext()->getHost();
        //====================================================================//
        // Detect Server Aliases
        foreach (self::HOSTS_ALIASES as $source => $target) {
            if (str_contains($source, $hostName)) {
                return $target;
            }
        }

        return $hostName;
    }
}
