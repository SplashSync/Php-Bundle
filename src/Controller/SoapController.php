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

namespace Splash\Bundle\Controller;

use SoapServer;
use Splash\Client\Splash;
use Splash\Server\SplashServer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Splash Bundle Soap Controller
 */
class SoapController extends AbstractController
{
    //====================================================================//
    //   WebService Available Functions
    //====================================================================//

    /**
     * Splash SOAP Ping Action
     *
     * @return null|string
     */
    public function ping(): ?string
    {
        return (new SplashServer())->ping();
    }

    /**
     * Splash SOAP Connect Action
     *
     * @param string $webserviceId
     * @param string $data
     *
     * @return null|string
     */
    public function connect(string $webserviceId, string $data): ?string
    {
        $server = new SplashServer();
        $this->get('splash.connectors.manager')->identify($webserviceId);

        return $server->connect($webserviceId, $data);
    }

    /**
     * Splash SOAP Admin Action
     *
     * @param string $webserviceId
     * @param string $data
     *
     * @return null|string
     */
    public function admin(string $webserviceId, string $data): ?string
    {
        $server = new SplashServer();
        $this->get('splash.connectors.manager')->identify($webserviceId);

        return $server->admin($webserviceId, $data);
    }

    /**
     * Splash SOAP Object Action
     *
     * @param string $webserviceId
     * @param string $data
     *
     * @return null|string
     */
    public function objects(string $webserviceId, string $data): ?string
    {
        $server = new SplashServer();
        $this->get('splash.connectors.manager')->identify($webserviceId);

        return $server->objects($webserviceId, $data);
    }

    /**
     * Splash SOAP File Action
     *
     * @param string $webserviceId
     * @param string $data
     *
     * @return null|string
     */
    public function files(string $webserviceId, string $data): ?string
    {
        $server = new SplashServer();
        $this->get('splash.connectors.manager')->identify($webserviceId);

        return $server->files($webserviceId, $data);
    }

    /**
     * Splash SOAP Widget Action
     *
     * @param string $webserviceId
     * @param string $data
     *
     * @return null|string
     */
    public function widgets(string $webserviceId, string $data): ?string
    {
        $server = new SplashServer();
        $this->get('splash.connectors.manager')->identify($webserviceId);

        return $server->widgets($webserviceId, $data);
    }

    //====================================================================//
    //   WebService SOAP Calls Responses Functions
    //====================================================================//

    /**
     * Execute Main External SOAP Requests.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function mainAction(Request $request): Response
    {
        //====================================================================//
        // Setup Php Specific Settings
        ini_set('display_errors', "0");
        error_reporting(E_ERROR);
        define('SPLASH_SERVER_MODE', 1);
        //====================================================================//
        // Detect NuSOAP requests send by Splash Server
        $userAgent = $request->headers->get('User-Agent');
        if (is_string($userAgent) && (false !== strpos($userAgent, 'SOAP'))) {
            //====================================================================//
            // Create SOAP Server
            $server = new SoapServer(
                dirname(__DIR__).'/Resources/wsdl/splash.wsdl',
                array('cache_wsdl' => WSDL_CACHE_NONE)
            );
            //====================================================================//
            // Register SOAP Service
            $server->setObject($this);
            //====================================================================//
            // Register shutdown method available for fatal errors retrieval
            register_shutdown_function(array(self::class, 'fatalHandler'));
            //====================================================================//
            // Prepare Response
            $response = new Response();
            $response->headers->set('Content-Type', 'text/xml; charset=ISO-8859-1');
            //====================================================================//
            // Execute Actions
            ob_start();
            $server->handle();
            $response->setContent(ob_get_clean());
            //====================================================================//
            // Return response
            return $response;
        }
        /** @phpstan-ignore-next-line */
        $webserviceId = (string) $request->get('node');
        if (!empty($webserviceId) && $this->get('splash.connectors.manager')->identify($webserviceId)) {
            Splash::log()->deb('Splash Started In System Debug Mode');
            //====================================================================//
            // Setup Php Errors Settings
            ini_set('display_errors', "1");
            error_reporting(E_ALL);
            //====================================================================//
            // Output Server Analyze & Debug
            $html = SplashServer::getStatusInformations();
            //====================================================================//
            // Output Module Complete Log
            $html .= Splash::log()->getHtmlLogList();
            //====================================================================//
            // Return Debug Response
            return new Response($html);
        }
        //====================================================================//
        // Return Empty Response
        return new Response('This WebService Provide no Description.');
    }

    /**
     * Test & Validate Splash SOAP Server Configuration.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function testAction(Request $request): Response
    {
        //====================================================================//
        // Identify Requested Webservice
        /** @phpstan-ignore-next-line */
        $webserviceId = (string) $request->get('node');
        if (empty($webserviceId) || empty($this->get('splash.connectors.manager')->identify($webserviceId))) {
            //====================================================================//
            // Return Empty Response
            return new Response('This WebService Provide no Description.');
        }

        //====================================================================//
        // Execute Splash Tests
        $results = array();
        //====================================================================//
        // Execute Splash Self-Test
        $results['selftest'] = Splash::SelfTest();
        if ($results['selftest']) {
            Splash::log()->msg('Self-Test Passed');
        }
        $logSelfTest = Splash::log()->GetHtmlLog(true);
        //====================================================================//
        // Execute Splash Ping Test
        $results['ping'] = Splash::Ping();
        $logPingTest = Splash::log()->GetHtmlLog(true);
        //====================================================================//
        // Execute Splash Connect Test
        $results['connect'] = Splash::Connect();
        $logConnectTest = Splash::log()->GetHtmlLog(true);

        //====================================================================//
        // Render Results
        return $this->render('SplashBundle::index.html.twig', array(
            'results' => $results,
            'selftest' => $logSelfTest,
            'ping' => $logPingTest,
            'connect' => $logConnectTest,
            'objects' => Splash::Objects(),
            'widgets' => Splash::Widgets(),
        ));
    }

    /**
     * Declare fatal Error Handler => Called in case of Script Exceptions
     *
     * @return void
     */
    public static function fatalHandler(): void
    {
        //====================================================================//
        // Read Last Error
        $error = error_get_last();
        if (!$error) {
            return;
        }
        //====================================================================//
        // Non Fatal Error
        if (E_ERROR != $error['type']) {
            Splash::log()->war($error['message'].' on File '.$error['file'].' Line '.$error['line']);

            return;
        }

        //====================================================================//
        // Fatal Error
        //====================================================================//

        //====================================================================//
        // Parse Error in Response.
        Splash::com()->fault($error);
        //====================================================================//
        // Process methods & Return the results.
        Splash::com()->handle();
    }
}
