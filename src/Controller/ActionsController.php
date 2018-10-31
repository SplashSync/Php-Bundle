<?php

namespace Splash\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Splash\Server\SplashServer;
use Splash\Client\Splash;

use Splash\Bundle\Interfaces\ConnectorInterface;

class ActionsController extends Controller
{
    //====================================================================//
    //   Redirect to Connectors Defined Actions
    //====================================================================//
    
    /**
     * @abstract    Redirect to Connectors Defined Actions
     *
     * @param   string  $ConnectorName
     * @param   string  $WebserviceId
     * @param   string  $Action
     *
     * @return  Response
     */
    public function indexAction(string $ConnectorName, string $WebserviceId, string $Action)
    {
        //====================================================================//
        // Load Connector Manager
        $Manager    =   $this->get("splash.connectors.manager");
        //====================================================================//
        // Seach for This Connection in Local Configuration
        $Connector   =   $Manager->get($WebserviceId);
        //====================================================================//
        // Safety Check
        if (!($ControllerAction = $this->validate($Connector, $ConnectorName, $Action))) {
            //====================================================================//
            // Return Empty Response
            return new Response("This WebService Provide no Description.");
        }
        //====================================================================//
        // Setup Php Specific Settings
        ini_set('display_errors', 0);
        error_reporting(E_ERROR);
        define("SPLASH_SERVER_MODE", 1);
        //====================================================================//
        // Boot Local Splash Module
        Splash::local()->boot($Manager, $this->get("router"));
        //====================================================================//
        // Setup Local Splash Module for Current Server
        Splash::local()->identify($WebserviceId);
        //====================================================================//
        // Redirect to Requested Conroller Action
        try {
            $Response   =   $this->forward($ControllerAction);
        } catch (\InvalidArgumentException $e) {
            //====================================================================//
            // Return Empty Response
            return new Response($e->getMessage());
        }
        return $Response;
    }
    
    /**
     * @abstract    Validate Controller Action Request
     *
     * @param   ConnectorInterface $Connector
     * @param   string  $ConnectorName
     * @param   string  $Action
     *
     * @return  string|false
     */
    public function validate(ConnectorInterface $Connector, string $ConnectorName, string $Action)
    {
        //====================================================================//
        // Safety Check - Connector Exists
        if (!$Connector) {
            return false;
        }
        //====================================================================//
        // Safety Check - Connector Name is Similar
        $Profile    =   $Connector->getProfile();
        if (!isset($Profile["name"]) || (strtolower($ConnectorName) != strtolower($Profile["name"]) )) {
            return false;
        }
        //====================================================================//
        // Safety Check - Connector Action Exists
        $Actions    =   $Connector->getAvailableActions();
        if (!isset($Actions[strtolower($Action)]) || empty($Actions[strtolower($Action)])) {
            return false;
        }
        //====================================================================//
        // Safety Check - Action Controller Exists
        // TODO
        
        return $Actions[strtolower($Action)];
    }
}
