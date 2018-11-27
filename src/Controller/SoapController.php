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
    
    public function connect($webserviceId, $data)
    {
        $server = new SplashServer();
        $this->get("splash.connectors.manager")->identify($webserviceId);

        return $server->connect($webserviceId, $data);
    }
    
    public function admin($webserviceId, $data)
    {
        $server = new SplashServer();
        $this->get("splash.connectors.manager")->identify($webserviceId);

        return $server->admin($webserviceId, $data);
    }
    
    public function objects($webserviceId, $data)
    {
        $server = new SplashServer();
        $this->get("splash.connectors.manager")->identify($webserviceId);

        return $server->objects($webserviceId, $data);
    }
    
    public function files($webserviceId, $data)
    {
        $server = new SplashServer();
        $this->get("splash.connectors.manager")->identify($webserviceId);

        return $server->files($webserviceId, $data);
    }
    
    public function widgets($webserviceId, $data)
    {
        $server = new SplashServer();
        $this->get("splash.connectors.manager")->identify($webserviceId);

        return $server->widgets($webserviceId, $data);
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
        // Detect NuSOAP requests send by Splash Server
        $userAgent  =   $request->headers->get('User-Agent');
        if (is_string($userAgent) && (strpos($userAgent, "SOAP") !== false)) {
            //====================================================================//
            // Create SOAP Server
            $server = new \SoapServer(
                dirname(__DIR__)."/Resources/wsdl/splash.wsdl",
                array('cache_wsdl' => WSDL_CACHE_NONE)
            );
            //====================================================================//
            // Register SOAP Service
            $server->setObject($this);
            //====================================================================//
            // Register shuttdown method available for fatal errors reteival
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
        } elseif (!empty($request->get("node")) && $this->get("splash.connectors.manager")->identify($request->get("node"))) {
            Splash::log()->deb("Splash Started In System Debug Mode");
            //====================================================================//
            // Setup Php Errors Settings
            ini_set('display_errors', 1);
            error_reporting(E_ALL);
            //====================================================================//
            // Output Server Analyze & Debug
            $html   =   SplashServer::getStatusInformations();
            //====================================================================//
            // Output Module Complete Log
            $html  .=   Splash::log()->getHtmlLogList();
//            $Html  .=   print_r(Splash::informations(), true);
            //====================================================================//
            // Return Debug Response
            return new Response($html);
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
        // Identify Requeted Webservice
        $webserviceId =   $request->get("node");
        if (empty($webserviceId) || empty($this->get("splash.connectors.manager")->identify($webserviceId))) {
            //====================================================================//
            // Return Empty Response
            return new Response("This WebService Provide no Description.");
        }
        
        //====================================================================//
        // Execute Splash Tests
        $results = array();
        //====================================================================//
        // Execute Splash Self-Test
        $results['selftest'] = Splash::SelfTest();
        if ($results['selftest']) {
            Splash::log()->msg("Self-Test Passed");
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
            "results"   =>  $results,
            "selftest"  =>  $logSelfTest,
            "ping"      =>  $logPingTest,
            "connect"   =>  $logConnectTest,
            "objects"   =>  Splash::Objects(),
            "widgets"   =>  Splash::Widgets(),
        ));
    }
    
    /*
     * @abstract   Declare fatal Error Handler => Called in case of Script Exceptions
     */
    function fatalHandler()
    {
        //====================================================================//
        // Read Last Error
        $error  =   error_get_last();
        if (!$error) {
            return;
        }
        //====================================================================//
        // Non Fatal Error
        if ($error["type"] != E_ERROR) {
            Splash::log()->war($error["message"]." on File ".$error["file"]." Line ".$error["line"]);

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
