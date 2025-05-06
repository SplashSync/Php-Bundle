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

namespace Splash\Bundle\Phpunit\Assertions;

use PHPUnit\Framework\Assert;
use Splash\Bundle\Dictionary\SplashBundleRoutes;
use Splash\Bundle\Models\AbstractConnector;
use Splash\Bundle\Phpunit\SymfonyBridge;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

/**
 * Collection of PhpUnit Assertions Dedicated to Connectors Testing
 */
class ConnectorValidator
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
    public static function assertMasterActionWorks(
        AbstractConnector $connector,
        array $data = array(),
        string $method = 'GET'
    ): Crawler {
        return self::assertRouteWorks(
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
    public static function assertMasterActionFail(
        AbstractConnector $connector,
        array $data = array(),
        string $method = 'GET'
    ): Crawler {
        return self::assertRouteFail(
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
    public static function assertPublicActionWorks(
        AbstractConnector $connector,
        string $action = null,
        array $data = array(),
        string $method = 'GET'
    ): Crawler {
        return self::assertRouteWorks(
            SplashBundleRoutes::PUBLIC,
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
    public static function assertPublicActionFail(
        AbstractConnector $connector,
        string $action = null,
        array $data = array(),
        string $method = 'GET'
    ): Crawler {
        return self::assertRouteFail(
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
    public static function assertSecuredActionWorks(
        AbstractConnector $connector,
        string $action,
        array $data = array(),
        string $method = 'GET'
    ): Crawler {
        return self::assertRouteWorks(
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
    public static function assertSecuredActionFail(
        AbstractConnector $connector,
        string $action,
        array $data = array(),
        string $method = 'GET'
    ): Crawler {
        return self::assertRouteFail(
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
    public static function generateUrl(string $route, array $parameters = array())
    {
        //====================================================================//
        // Generate Url
        return SymfonyBridge::getRouter()->generate($route, $parameters);
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
    public static function assertRouteWorks(
        string $route,
        array $parameters = array(),
        array $data = array(),
        string $method = 'GET'
    ): Crawler {
        $client = SymfonyBridge::getTestClient();
        //====================================================================//
        // Generate Url
        $url = self::generateUrl($route, $parameters);
        //====================================================================//
        // Execute Client Request
        $client->followRedirects();
        $client->setMaxRedirects(3);
        //====================================================================//
        // Detect JSON POST Mode
        if ("JSON" == $method) {
            $server = array("CONTENT_TYPE" => "application/json");
            $jsonData = (string) json_encode($data);
            $crawler = $client->request("POST", $url, array(), array(), $server, $jsonData);
        } else {
            $crawler = $client->request($method, $url, $data);
        }
        Assert::assertInstanceOf(Crawler::class, $crawler);

        //====================================================================//
        // Verify Response Was Ok
        $response = $client->getResponse();
        Assert::assertInstanceOf(Response::class, $response);
        if (!$response->isSuccessful()) {
            try {
                print_r($crawler->filterXPath('//*[@class="stacktrace"]')->first()->html());
            } catch (\Exception $e) {
                print_r(substr((string) $response->getContent(), 0, 2000));
            }
        }
        Assert::assertTrue(
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
    public static function assertRouteFail(
        string $route,
        array $parameters = array(),
        array $data = array(),
        string $method = 'GET'
    ): Crawler {
        $client = SymfonyBridge::getTestClient();
        //====================================================================//
        // Generate Url
        $url = self::generateUrl($route, $parameters);

        //====================================================================//
        // Execute Client Request
        $client->followRedirects();
        $client->setMaxRedirects(3);
        //====================================================================//
        // Detect JSON POST Mode
        if ("JSON" == $method) {
            $jsonData = (string) json_encode($data);
            $server = array("CONTENT_TYPE" => "application/json");
            $crawler = $client->request("POST", $url, array(), array(), $server, $jsonData);
        } else {
            $crawler = $client->request($method, $url, $data);
        }
        Assert::assertInstanceOf(Crawler::class, $crawler);

        //====================================================================//
        // Verify Response Was Ko
        $response = $client->getResponse();
        Assert::assertInstanceOf(Response::class, $response);
        Assert::assertFalse(
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
        $response = $this->getTestClient()->getResponse();
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
        $response = $this->getTestClient()->getInternalResponse();
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
