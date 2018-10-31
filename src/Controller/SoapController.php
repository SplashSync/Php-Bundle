<?php

namespace Splash\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Splash\Server\SplashServer;
use Splash\Client\Splash;

class SoapController extends Controller
{

    //====================================================================//
    //   WebService Available Functions
    //====================================================================//

    public function ping()
    {
        return (new SplashServer())->ping();
    }
    
    public function connect($WsId, $data)
    {
        $server = new SplashServer();
        Splash::local()->identify($WsId);
        return $server->connect($WsId, $data);
    }
    
    public function admin($WsId, $data)
    {
        $server = new SplashServer();
        Splash::local()->identify($WsId);
        return $server->admin($WsId, $data);
    }
    
    public function objects($WsId, $data)
    {
        $server = new SplashServer();
        Splash::local()->identify($WsId);
        return $server->objects($WsId, $data);
    }
    
    public function files($WsId, $data)
    {
        $server = new SplashServer();
        Splash::local()->identify($WsId);
        return $server->files($WsId, $data);
    }
    
    public function widgets($WsId, $data)
    {
        $server = new SplashServer();
        Splash::local()->identify($WsId);
        return $server->widgets($WsId, $data);
    }

    //====================================================================//
    //   WebService SOAP Calls Responses Functions
    //====================================================================//
    
    /**
     * Execute Main External SOAP Requests
     */
    public function mainAction(Request $request)
    {
        //====================================================================//
        // Setup Php Specific Settings
        ini_set('display_errors', 0);
        error_reporting(E_ERROR);
        define("SPLASH_SERVER_MODE", 1);
        //====================================================================//
        // Boot Local Splash Module
        Splash::local()->boot($this->get("splash.connectors.manager"), $this->get("router"));
        //====================================================================//
        // Detect NuSOAP requests send by Splash Server
        if (strpos($request->headers->get('User-Agent'), "SOAP") !== false) {
            //====================================================================//
            // Create SOAP Server
            $server = new \SoapServer(
                dirname(__DIR__) . "/Resources/wsdl/splash.wsdl",
                array('cache_wsdl' => WSDL_CACHE_NONE)
            );
            //====================================================================//
            // Register SOAP Service
            $server->setObject($this);
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
        } elseif (!empty($request->get("node")) && Splash::local()->identify($request->get("node"))) {
            Splash::log()->deb("Splash Started In System Debug Mode");
            //====================================================================//
            // Setup Php Errors Settings
            ini_set('display_errors', 1);
            error_reporting(E_ALL);
            //====================================================================//
            // Output Server Analyze & Debug
            $Html   =   SplashServer::getStatusInformations();
            //====================================================================//
            // Output Module Complete Log
            $Html  .=   Splash::log()->getHtmlLogList();
//            $Html  .=   print_r(Splash::informations(), true);
            //====================================================================//
            // Return Debug Response
            return new Response($Html);
        }
        //====================================================================//
        // Return Empty Response
        return new Response("This WebService Provide no Description.");
    }
    
    /**
     * Test & Validate Splash SOAP Server Configuration
     */
    public function testAction(Request $request)
    {
        //====================================================================//
        // Boot Local Splash Module
        Splash::local()->boot($this->get("splash.connectors.manager"), $this->get("router"));

        //====================================================================//
        // Identify Requeted Webservice
        $NodeId =   $request->get("node");
        if (empty($NodeId) || empty(Splash::local()->identify($NodeId))) {
            //====================================================================//
            // Return Empty Response
            return new Response("This WebService Provide no Description.");
        }
        
        //====================================================================//
        // Execute Splash Tests
        $Results = array();
        //====================================================================//
        // Execute Splash Self-Test
        $Results['selftest'] = Splash::SelfTest();
        if ($Results['selftest']) {
            Splash::log()->msg("Self-Test Passed");
        }
        $SelfTest_Log = Splash::log()->GetHtmlLog(true);
        //====================================================================//
        // Execute Splash Ping Test
        $Results['ping'] = Splash::Ping();
        $PingTest_Log = Splash::log()->GetHtmlLog(true);
        //====================================================================//
        // Execute Splash Connect Test
        $Results['connect'] = Splash::Connect();
        $ConnectTest_Log = Splash::log()->GetHtmlLog(true);
                
        //====================================================================//
        // Render Results
        return $this->render('SplashBundle::index.html.twig', array(
            "results"   =>  $Results,
            "selftest"  =>  $SelfTest_Log,
            "ping"      =>  $PingTest_Log,
            "connect"   =>  $ConnectTest_Log,
            "objects"   =>  Splash::Objects(),
            "widgets"   =>  Splash::Widgets(),
        ));
    }
}
