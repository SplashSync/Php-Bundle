<?php
/**
 * This file is part of SplashSync Project.
 *
 * Copyright (C) Splash Sync <www.splashsync.com>
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Bernard Paquier <contact@splashsync.com>
 */

namespace Splash\Bundle\Models\Manager;

use ArrayObject;

use Symfony\Component\Routing\RouterInterface;

use Splash\Core\SplashCore as Splash;

use Splash\Bundle\Models\ConnectorInterface;

/**
 * @abstract    WebService Manager for Spash Connectors
 */
trait WebserviceTrait
{
    
    /**
     * @var string
     */
    private $ServerId;
    
    /**
     * @var RouterInterface;
     */
    private $Router;
    
    //====================================================================//
    // *******************************************************************//
    //  MANDATORY CORE MODULE LOCAL FUNCTIONS
    // *******************************************************************//
    //====================================================================//
    
    /**
     *      @abstract       Return Local Server Parameters as Aarray
     *
     *      THIS FUNCTION IS MANDATORY
     *
     *      This function called on each initialisation of the module
     *
     *      Result must be an array including mandatory parameters as strings
     *         ["WsIdentifier"]         =>>  Name of Module Default Language
     *         ["WsEncryptionKey"]      =>>  Name of Module Default Language
     *         ["DefaultLanguage"]      =>>  Name of Module Default Language
     *
     *      @return         array       $parameters
     */
    public function parameters()
    {
        $Parameters       =     array();
        //====================================================================//
        // Safety Check - Server Indetify Passed
        if (!$this->ServerId) {
            return $Parameters;
        }
        //====================================================================//
        // Server Identification Parameters
        $Parameters["WsIdentifier"]         =   $this->getWebserviceId($this->ServerId);
        $Parameters["WsEncryptionKey"]      =   $this->getWebserviceKey($this->ServerId);
        //====================================================================//
        // If Expert Mode => Overide of Server Host Address
        if (!empty($this->getWebserviceHost($this->ServerId))) {
            $Parameters["WsHost"]           =   $this->getWebserviceHost($this->ServerId);
        }
        //====================================================================//
        // Use of Symfony Routes => Overide of Local Server Path Address
        if ($this->Router) {
            $Parameters["ServerPath"]      =   $this->Router->generate("splash_main_soap");
        }
        return $Parameters;
    }
    
    /**
     *      @abstract       Include Local Includes Files
     *
     *      Include here any local files required by local functions.
     *      This Function is called each time the module is loaded
     *
     *      There may be differents scenarios depending if module is
     *      loaded as a library or as a NuSOAP Server.
     *
     *      This is triggered by global constant SPLASH_SERVER_MODE.
     *
     *      @return         bool
     */
    public function includes()
    {
        return true;
    }
           
    /**
     *      @abstract       Return Local Server Self Test Result
     *
     *      THIS FUNCTION IS MANDATORY
     *
     *      This function called during Server Validation Process
     *
     *      We recommand using this function to validate all functions or parameters
     *      that may be required by Objects, Widgets or any other modul specific action.
     *
     *      Use Module Logging system & translation tools to retrun test results Logs
     *
     *      @return         bool    global test result
     */
    public function selfTest()
    {
        //====================================================================//
        //  Load Local Translation File
        Splash::Translator()->Load("main@local");

        //====================================================================//
        //  Verify - Router is Given
        if (empty($this->Router)) {
            return Splash::log()->err("No Router: When Connector Manager is"
                    . " Activated as WebService, Router MUST be setuped.");
        }
        
        //====================================================================//
        //  Verify - Server Identifier Given
        if (empty($this->getWebserviceId($this->ServerId))) {
            return Splash::log()->err("ErrSelfTestNoWsId");
        }
        
        //====================================================================//
        //  Verify - Server Encrypt Key Given
        if (empty($this->getWebserviceKey($this->ServerId))) {
            return Splash::log()->err("ErrSelfTestNoWsKey");
        }
        
        return true;
    }
    
    /**
     *  @abstract   Update Server Informations with local Data
     *
     *  @param      ArrayObject     $Informations   Informations Inputs
     *
     *  @return     ArrayObject
     */
    public function informations($Informations)
    {
        return $this->get($this->ServerId)->informations($Informations);
    }
    
    //====================================================================//
    // *******************************************************************//
    //  OPTIONNAl CORE MODULE LOCAL FUNCTIONS
    // *******************************************************************//
    //====================================================================//
    
    /**
     * @abstract       Return Local Server Test Sequences as Aarray
     *
     *      THIS FUNCTION IS OPTIONNAL - USE IT ONLY IF REQUIRED
     *
     *      This function called on each initialization of module's tests sequences.
     *      It's aim is to list different configurations for testing on local system.
     *
     *      If Name = List, Result must be an array including list of Sequences Names.
     *
     *      If Name = ASequenceName, Function will Setup Sequence on Local System.
     *
     * @return         array       $Sequences
     */
    public function testSequences($Name = null)
    {
        //====================================================================//
        // Load Configured Servers List
        $ServersList    =   $this->getServersNames();
        //====================================================================//
        // Generate Sequence List
        if ($Name == "List") {
            return $ServersList;
        }
        //====================================================================//
        // Identify Server by Name
        if (!in_array($Name, $ServersList)) {
            $this->identify(array_search($Name, $ServersList));
        }
        return array();
    }
    
    /**
     *      @abstract       Return Local Server Test Parameters as Array
     *
     *      THIS FUNCTION IS OPTIONNAL - USE IT ONLY IF REQUIRED
     *
     *      This function called on each initialization of module's tests sequences.
     *      It's aim is to override general Tests settings to be adjusted to local system.
     *
     *      Result must be an array including parameters as strings or array.
     *
     *      @see Splash\Tests\Tools\ObjectsCase::settings for objects tests settings
     *
     *      @return         array       $parameters
     */
    public function testParameters()
    {
        //====================================================================//
        // Init Parameters Array
        $Parameters       =     array();
        
//        //====================================================================//
//        //  Load Locales Parameters
//        if ($this->getContainer()->hasParameter("locales")) {
//            $Parameters["Langs"] = $this->getContainer()->getParameter("locales");
//        } else {
//            $Parameters["Langs"] = array($this->getContainer()->getParameter("locale"));
//        }
        
        return $Parameters;
    }
    
    
    //====================================================================//
    // *******************************************************************//
    //  OVERRIDING CORE MODULE LOCAL FUNCTIONS
    // *******************************************************************//
    //====================================================================//
    
    /**
     *      @abstract   Build list of Available Objects
     *
     *      @return     array       $list           list array including all available Objects Type
     */
    public function objects()
    {
        //====================================================================//
        // Load Objects Type List
        return $this->get($this->ServerId)->objects();
    }

    /**
     *      @abstract   Get Specific Object Class
     *                  This function is a router for all local object classes & functions
     *
     *      @params     $type       Specify Object Class Name
     *
     *      @return     mixed
     */
    public function object($ObjectType = null)
    {
        return $this->get($this->ServerId)->object($ObjectType);
    }

    /**
     *      @abstract   Build list of Available Widgets
     *
     *      @return     array       $list           list array including all available Widgets Type
     */
    public function widgets()
    {
        return array("SelfTest");
//        //====================================================================//
//        // Init Annotations Manager
//        if (is_null($this->_wm)) {
//            //====================================================================//
//            // Create Annotations Manager
//            $this->_wm = new WidgetAnnotations($this->getParameter("widgets"));
//        }
//
//        //====================================================================//
//        // Load Widgets Type List
//        return $this->_wm->getWidgetsTypes();
    }

    /**
     *      @abstract   Get Specific Widgets Class
     *                  This function is a router for all local Widgets classes & functions
     *
     *      @params     $type       Specify Widgets Class Name
     *
     *      @return     mixed
     */
    public function widget($WidgetType = null)
    {
        $WidgetType;
//        //====================================================================//
//        // Check in Cache
//        $Index = (is_null($WidgetType) ? "__CORE__" : $WidgetType);
//        if (array_key_exists($Index, $this->widgets)) {
//            return $this->widgets[$Index];
//        }
//
//        //====================================================================//
//        // Init Annotations Manager
//        if (is_null($this->_wm)) {
//            //====================================================================//
//            // Create Annotations Manager
//            $this->_wm = new WidgetAnnotations($this->getParameter("widgets"));
//        }
//
//        //====================================================================//
//        // Initialize Local Widget Annotation
//        $this->widgets[$Index] = $this->_wm->getAnnotations($WidgetType);
//        //====================================================================//
//        // Setup Local Widget Annotation
//        if ($this->widgets[$Index]) {
//            $this->widgets[$Index]->setContainer($this->getContainer());
//        }
//
//        return $this->widgets[$Index];
    }
    
    //====================================================================//
    //  VARIOUS LOW LEVEL FUNCTIONS
    //====================================================================//

    /**
     * @abstract    Setup Symfony Router
     * @param       RouterInterface $Router
     * @return      void
     */
    public function setRouter(RouterInterface $Router)
    {
        $this->Router    =   $Router;
    }
    
    /**
     * @abstract    Setup Current Server Id
     * @param       string  $ServerId
     * @return      void
     */
    public function setCurrent(string $ServerId)
    {
        $this->ServerId    =   $ServerId;
    }
}
