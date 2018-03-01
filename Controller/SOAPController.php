<?php

namespace Splash\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Splash\Server\SplashServer;
use Splash\Client\Splash;

class SOAPController extends Controller
{

    //====================================================================//
    //   WebService Available Functions
    //====================================================================//  

    function Ping($id)                      {   return SplashServer::Ping($id);     }
    function Connect($id,$data)             {   $server = new SplashServer(); return $server->Connect($id,$data);   }
    function Admin($id,$data)               {   $server = new SplashServer(); return $server->Admin($id,$data);     }
    function Objects($id,$data)             {   $server = new SplashServer(); return $server->Objects($id,$data);   }
    function Files($id,$data)               {   $server = new SplashServer(); return $server->Files($id,$data);     }
    function Widgets($id,$data)             {   $server = new SplashServer(); return $server->Widgets($id,$data);   }

    //====================================================================//
    //   WebService SOAP Calls Responses Functions
    //====================================================================//  
    
    /**
     * Execute Main External SOAP Requests
     */
    public function mainAction(Request $request)
    {
        //====================================================================//  
        // Detect NuSOAP requests send by Splash Server 
        if ( strpos( $request->headers->get('User-Agent') , "SOAP" ) === FALSE )
        {
            //====================================================================//
            // Return Empty Response
            return new Response("This WebService Provide no Description.");
        }
        
        //====================================================================//
        // Setup Php Specific Settings
        ini_set('display_errors', 0);
        error_reporting(E_ERROR);
    
        define("SPLASH_SERVER_MODE" , 1);
        
        //====================================================================//
        // Boot Local Splash Module
        Splash::Local()->Boot($this->container);
        
        //====================================================================//
        // Create SOAP Server
        $server = new \SoapServer(dirname(__DIR__) . "/Resources/wsdl/splash.wsdl", array('cache_wsdl' => WSDL_CACHE_NONE));
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
    }     
    
    /**
     * Test & Validate Splash SOAP Server Configuration
     */
    public function testAction()
    {
        
        //====================================================================//
        // Boot Local Splash Module
        Splash::Local()->Boot($this->container);

        $Results = array();
        
        //====================================================================//
        // Execute Splash Self-Test
        $Results['selftest'] = Splash::SelfTest();
        if ( $Results['selftest'] ) {
            Splash::Log()->msg("Self-Test Passed");
        }
        $SelfTest_Log = Splash::Log()->GetHtmlLog(True);

        //====================================================================//
        // Execute Splash Ping Test
        $Results['ping'] = Splash::Ping();
        $PingTest_Log = Splash::Log()->GetHtmlLog(True);
        
        //====================================================================//
        // Execute Splash Connect Test
        $Results['connect'] = Splash::Connect();
        $ConnectTest_Log = Splash::Log()->GetHtmlLog(True);
                
        return $this->render('SplashBundle::index.html.twig',array(
            "results"   =>  $Results,
            "selftest"  =>  $SelfTest_Log,
            "ping"      =>  $PingTest_Log,
            "connect"   =>  $ConnectTest_Log,
            "objects"   =>  Splash::Objects(),
            "widgets"   =>  Splash::Widgets(),
        )); 
                
    }
    
}
