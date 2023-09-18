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

namespace Splash\Bundle\Tests;

use Splash\Bundle\Models\AbstractConnector;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

/**
 * Collection of PhpUnit Assertions Dedicated to Connectors Testing
 */
trait ConnectorAssertTrait
{
    /**
     * Ensure a Connector Master Action Works.
     *
     * @param AbstractConnector $connector
     * @param array             $data
     * @param string            $method
     *
     * @return Crawler
     */
    public function assertMasterActionWorks(
        AbstractConnector $connector,
        array $data = array(),
        string $method = 'GET'
    ): Crawler {
        return $this->assertRouteWorks(
            "splash_connector_action_master",
            array("connectorName" => $connector->getProfile()["name"]),
            $data,
            $method
        );
    }

    /**
     * Ensure a Connector Master Action Fail.
     *
     * @param AbstractConnector $connector
     * @param array             $data
     * @param string            $method
     *
     * @return Crawler
     */
    public function assertMasterActionFail(
        AbstractConnector $connector,
        array $data = array(),
        string $method = 'GET'
    ): Crawler {
        return $this->assertRouteFail(
            "splash_connector_action_master",
            array("connectorName" => $connector->getProfile()["name"]),
            $data,
            $method
        );
    }

    /**
     * Ensure a Connector Public Action Works.
     *
     * @param AbstractConnector $connector
     * @param null|string       $action
     * @param array             $data
     * @param string            $method
     *
     * @return Crawler
     */
    public function assertPublicActionWorks(
        AbstractConnector $connector,
        string $action = null,
        array $data = array(),
        string $method = 'GET'
    ): Crawler {
        return $this->assertRouteWorks(
            "splash_connector_action",
            self::getRouteParameters($connector, $action),
            $data,
            $method
        );
    }

    /**
     * Ensure a Connector Public Action Fail.
     *
     * @param AbstractConnector $connector
     * @param string            $action
     * @param array             $data
     * @param string            $method
     *
     * @return Crawler
     */
    public function assertPublicActionFail(
        AbstractConnector $connector,
        string $action = null,
        array $data = array(),
        string $method = 'GET'
    ): Crawler {
        return $this->assertRouteFail(
            "splash_connector_action",
            self::getRouteParameters($connector, $action),
            $data,
            $method
        );
    }

    /**
     * Ensure a Connector Secured Action Works.
     *
     * @param AbstractConnector $connector
     * @param string            $action
     * @param array             $data
     * @param string            $method
     *
     * @return Crawler
     */
    public function assertSecuredActionWorks(
        AbstractConnector $connector,
        string $action,
        array $data = array(),
        string $method = 'GET'
    ): Crawler {
        return $this->assertRouteWorks(
            "splash_connector_secured_action",
            self::getRouteParameters($connector, $action),
            $data,
            $method
        );
    }

    /**
     * Ensure a Connector Secured Action Fail.
     *
     * @param AbstractConnector $connector
     * @param string            $action
     * @param array             $data
     * @param string            $method
     *
     * @return Crawler
     */
    public function assertSecuredActionFail(
        AbstractConnector $connector,
        string $action,
        array $data = array(),
        string $method = 'GET'
    ): Crawler {
        return $this->assertRouteFail(
            "splash_connector_secured_action",
            self::getRouteParameters($connector, $action),
            $data,
            $method
        );
    }

    /**
     * Generate Route Url
     *
     * @param string $route
     * @param array  $parameters
     *
     * @return string
     */
    public function generateUrl(string $route, array $parameters = array())
    {
        //====================================================================//
        // Generate Url
        return $this->getRouter()->generate($route, $parameters);
    }

    /**
     * Ensure a Route Works.
     *
     * @param string $route
     * @param array  $parameters
     * @param array  $data
     * @param string $method
     *
     * @return Crawler
     */
    public function assertRouteWorks(
        string $route,
        array $parameters = array(),
        array $data = array(),
        string $method = 'GET'
    ): Crawler {
        //====================================================================//
        // Generate Url
        $url = $this->generateUrl($route, $parameters);

        //====================================================================//
        // Execute Client Request
        $this->getTestClient()->followRedirects();
        $this->getTestClient()->setMaxRedirects(3);
        //====================================================================//
        // Detect JSON POST Mode
        if ("JSON" == $method) {
            $server = array("CONTENT_TYPE" => "application/json");
            $jsonData = (string) json_encode($data);
            $crawler = $this->getTestClient()->request("POST", $url, array(), array(), $server, $jsonData);
        } else {
            $crawler = $this->getTestClient()->request($method, $url, $data);
        }
        $this->assertInstanceOf(Crawler::class, $crawler);

        //====================================================================//
        // Verify Response Was Ok
        $response = $this->getTestClient()->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        if (!$response->isSuccessful()) {
            print_r(substr((string) $response->getContent(), 0, 2000));
        }
        $this->assertTrue(
            $response->isSuccessful(),
            'This Url Fail : '.$url.' Status Code : '.$response->getStatusCode()
        );

        return $crawler;
    }

    /**
     * Ensure a Route Fail.
     *
     * @param string $route
     * @param array  $parameters
     * @param array  $data
     * @param string $method
     *
     * @return Crawler
     */
    public function assertRouteFail(
        string $route,
        array $parameters = array(),
        array $data = array(),
        string $method = 'GET'
    ): Crawler {
        //====================================================================//
        // Generate Url
        $url = $this->generateUrl($route, $parameters);

        //====================================================================//
        // Execute Client Request
        $this->getTestClient()->followRedirects();
        $this->getTestClient()->setMaxRedirects(3);
        //====================================================================//
        // Detect JSON POST Mode
        if ("JSON" == $method) {
            $jsonData = (string) json_encode($data);
            $server = array("CONTENT_TYPE" => "application/json");
            $crawler = $this->getTestClient()->request("POST", $url, array(), array(), $server, $jsonData);
        } else {
            $crawler = $this->getTestClient()->request($method, $url, $data);
        }
        $this->assertInstanceOf(Crawler::class, $crawler);

        //====================================================================//
        // Verify Response Was Ko
        $response = $this->getTestClient()->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertFalse(
            $response->isSuccessful(),
            'This Url Should Fail but Works : '.$url.' Status Code : '.$response->getStatusCode()
        );

        return $crawler;
    }

    /**
     * Get Framework Client Response.
     *
     * @return string
     */
    public function getClientResponse() : string
    {
        //====================================================================//
        // Link to Symfony Router
        if (!isset($this->client)) {
            return "";
        }
        $response = $this->client->getResponse();
        if (!($response instanceof Response)) {
            return "";
        }

        return $response->__toString();
    }

    /**
     * Get Framework Client Response.
     *
     * @return string
     */
    public function getResponseContents() : string
    {
        //====================================================================//
        // Link to Symfony Router
        if (!isset($this->client)) {
            return "";
        }
        $response = $this->client->getInternalResponse();
        if (!($response instanceof \Symfony\Component\BrowserKit\Response)) {
            return "";
        }

        return $response->getContent();
    }

    /**
     * Get Action Route Parameters.
     *
     * @param AbstractConnector $connector
     * @param null|string       $action
     *
     * @return array
     */
    private static function getRouteParameters(AbstractConnector $connector, string $action = null): array
    {
        $parameters = array(
            "connectorName" => $connector->getProfile()["name"],
            "webserviceId" => $connector->getWebserviceId(),
        );
        if (!empty($action)) {
            $parameters["action"] = $action;
        }

        return $parameters;
    }
}
