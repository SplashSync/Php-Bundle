<?php

namespace Splash\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Splash\Server\SplashServer;
use Splash\Client\Splash;



use Doctrine\Common\Annotations\AnnotationReader;
use Splash\Bundle\Conversion\SplashFieldConverter;
//use Acme\DataBundle\Entity\Person;


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
        if ( strpos( $request->headers->get('User-Agent') , "NuSOAP" ) === FALSE )
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
        
//        //====================================================================//
//        // Boot Local Splash Module
//        Splash::Local()->Boot($this->container);
        
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

        //====================================================================//
        // Execute Splash Self-Test
        if ( Splash::SelfTest() ) {
            Splash::Log()->msg("Self-Test Passed");
        }
        $SelfTest_Log = Splash::Log()->GetHtmlLog(True);

        //====================================================================//
        // Execute Splash Ping Test
        Splash::Ping();
        $PingTest_Log = Splash::Log()->GetHtmlLog(True);
        
        //====================================================================//
        // Execute Splash Connect Test
        Splash::Connect();
        $ConnectTest_Log = Splash::Log()->GetHtmlLog(True);
                
        return $this->render('SplashBundle::self_test.html.twig',array(
            "selftest"  =>  $SelfTest_Log,
            "ping"      =>  $PingTest_Log,
            "connect"   =>  $ConnectTest_Log,
        )); 
                
    }
    
}
