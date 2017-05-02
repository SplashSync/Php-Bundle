<?php
/*
 * Copyright (C) 2011-2014  Bernard Paquier       <bernard.paquier@gmail.com>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 *
 * 
 *  \Id 	$Id: osws-local-Main.class.php 136 2014-10-12 22:33:28Z Nanard33 $
 *  \version    $Revision: 136 $
 *  \date       $LastChangedDate: 2014-10-13 00:33:28 +0200 (lun. 13 oct. 2014) $ 
 *  \ingroup    Splash - OpenSource Synchronisation Service
 *  \brief      Core Local Server Definition Class
 *  \class      SplashLocal
 *  \remarks	Designed for Splash Module - Dolibar ERP Version  
*/

namespace Splash\Local;

//use User;
//use ArrayObject;

//====================================================================//
//  INCLUDES
//====================================================================//

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

use Splash\Core\SplashCore          as Splash;

use Splash\Local\Objects\Manager    as ObjectsManager;
use Splash\Local\Objects\Annotations;

use Splash\Local\Widgets\Annotations    as  WidgetAnnotations;

//====================================================================//
//  CONSTANTS DEFINITION
//====================================================================//

//====================================================================//
//  CLASS DEFINITION
//====================================================================//

//====================================================================//
// *******************************************************************//
// *******************************************************************//
//====================================================================//
// 
//  MAIN CORE FUNCTION
//  
//  This Class includes all commons Local functions
//    
//====================================================================//
// *******************************************************************//
// *******************************************************************//
//====================================================================//
    
 /**
 *	\class      SplashLocal
 *	\brief      Local Core Management Class
 */
class Local
{
    use ContainerAwareTrait;
    
    /*
     * @var array
     */
    private $objects    = Array();
    
    /*
     * @var array
     */
    private $widgets    = Array();
    
    /*
     * @abstract    Splash Annotations Manager
     * @var \Splash\Local\Objects\Annotations
     */
    private $_am        = Null;    

    /*
     * @abstract    Splash Widget & Annotations Manager
     * @var \Splash\Local\Widgets\Annotations
     */
    private $_wm        = Null;
    
    /*
     * @abstract    Splash Bundle Configuration
     * @var array
     */
    private $config;
    
    //====================================================================//
    // General Class Variables	
    // Place Here Any SPECIFIC Variable for your Core Module Class
    //====================================================================//

    //====================================================================//
    // Class Constructor
    //====================================================================//
        
    /**
     *      @abstract       Class Constructor (Used only if localy necessary)
     *      @return         int                     0 if KO, >0 if OK
     */
    function __construct()
    {
        //====================================================================//
        // Place Here Any SPECIFIC Initialisation Code
        //====================================================================//
        return True;
    }

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
    public function Parameters()
    {
        
        $Parameters       =     array();

        //====================================================================//
        // Server Identification Parameters
        $Parameters["WsIdentifier"]         =   $this->getParameter("id");
        $Parameters["WsEncryptionKey"]      =   $this->getParameter("key");
        
        //====================================================================//
        // If Expert Mode => Overide of Server Host Address
        if ( !empty($this->getParameter("host")) ) {
            $Parameters["WsHost"]           =   $this->getParameter("host");
        }
        
        //====================================================================//
        // Use of Symfony Routes => Overide of Local Server Path Address
        if ($this->container) {
            $Parameters["ServerPath"]      =   $this->container->get('router')
                    ->generate("splash_main_soap");
        }
//        
//        //====================================================================//
//        // Overide Module Parameters with Local User Selected Lang
//        if ( $this->getParameter("SPLASH_LANG") ) {
//            $Parameters["DefaultLanguage"]      =   $this->getParameter("SPLASH_LANG");
//        //====================================================================//
//        // Overide Module Parameters with Local Default System Lang
//        } elseif ( ($langs) && $langs->getDefaultLang() ) {
//            $Parameters["DefaultLanguage"]      =   $langs->getDefaultLang();
//        } 
//        
//        //====================================================================//
//        // Overide Module Local Name in Logs
//        $Parameters["localname"]        =   $this->getParameter("MAIN_INFO_SOCIETE_NOM");
        
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
    public function Includes()
    {

        //====================================================================//
        // When Library is called in server mode ONLY
        //====================================================================//
        if ( SPLASH_SERVER_MODE )
        {
            // NOTHING TO DO 
        }

        //====================================================================//
        // When Library is called in client mode ONLY
        //====================================================================//
        else
        {
            // NOTHING TO DO 
        }

        //====================================================================//
        // When Library is called in both clinet & server mode
        //====================================================================//

        // NOTHING TO DO  
        
        return True;
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
    public function SelfTest()
    {
        //====================================================================//
        //  Load Local Translation File
        Splash::Translator()->Load("main@local");          

        //====================================================================//
        //  Verify - Container is Given
        if ( empty($this->container) ) {
            return Splash::Log()->Err("ErrNoContainer");
        }        
        
        //====================================================================//
        //  Verify - Server Identifier Given
        if ( empty($this->getParameter("id")) ) {
            return Splash::Log()->Err("ErrSelfTestNoWsId");
        }        
        
        //====================================================================//
        //  Verify - Server Encrypt Key Given
        if ( empty($this->getParameter("key")) ) {
            return Splash::Log()->Err("ErrSelfTestNoWsKey");
        }        
        
        return True;
        
        
        //====================================================================//
        //  Verify - User Selected
        if ( !isset($conf->global->SPLASH_USER) || ($conf->global->SPLASH_USER <= 0) ) {
            return Splash::Log()->Err("ErrSelfTestNoUser");
        }        
        
        //====================================================================//
        //  Verify - Stock Selected
        if ( !isset($conf->global->SPLASH_STOCK) || ($conf->global->SPLASH_STOCK <= 0) ) {
            return Splash::Log()->Err("ErrSelfTestNoStock");
        }        
        
        //====================================================================//
        // Check if company name is defined (first install)
        if (empty($conf->global->MAIN_INFO_SOCIETE_NOM) || empty($conf->global->MAIN_INFO_SOCIETE_COUNTRY))
        {
            return Splash::Log()->Err($langs->trans("WarningMandatorySetupNotComplete"));
        }

        Splash::Log()->Msg("MsgSelfTestOk");
        return True;
    }       
    
    /**
     *  @abstract   Update Server Informations with local Data
     * 
     *  @param     arrayobject  $Informations   Informations Inputs
     * 
     *  @return     arrayobject
     */
    public function Informations($Informations)
    {
        //====================================================================//
        // Init Response Object
        $Response = $Informations;
        
        //====================================================================//
        // Company Informations
        $Response->company          =   $this->getParameter("company",      "...", "infos");
        $Response->address          =   $this->getParameter("address",      "...", "infos");
        $Response->zip              =   $this->getParameter("zip",          "...", "infos");
        $Response->town             =   $this->getParameter("town",         "...", "infos");
        $Response->country          =   $this->getParameter("country",      "...", "infos");
        $Response->www              =   $this->getParameter("www",          "...", "infos");
        $Response->email            =   $this->getParameter("email",        "...", "infos");
        $Response->phone            =   $this->getParameter("phone",        "...", "infos");
        
        //====================================================================//
        // Server Logo & Images
        $icopath = $this->container->get('kernel')->getRootDir() . "/../web/favicon.ico"; 
        $Response->icoraw           =   Splash::File()->ReadFileContents(
                is_file($icopath) ? $icopath : (dirname(__DIR__) . "/Resources/public/symfony_ico.png")
                );

        if ($this->getParameter("logo",Null, "infos")) {
            $Response->logourl      =   (strpos($this->getParameter("logo",Null, "infos"), "http:///") == 0) ? Null : filter_input(INPUT_SERVER, "SERVER_NAME");
            $Response->logourl     .=   $this->getParameter("logo",Null, "infos");
        } else {
            $Response->logourl          =   "http://symfony.com/logos/symfony_black_03.png?v=5";
        }
        
        //====================================================================//
        // Server Informations
        $Response->servertype       =   "Symfony 2";
        $Response->serverurl        =   filter_input(INPUT_SERVER, "SERVER_NAME") ? filter_input(INPUT_SERVER, "SERVER_NAME") : "localhost:8000";
        
        return $Response;
    }    
    
//====================================================================//
// *******************************************************************//
//  OPTIONNAl CORE MODULE LOCAL FUNCTIONS
// *******************************************************************//
//====================================================================//
    
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
    public function TestParameters()
    {
        //====================================================================//
        // Init Parameters Array
        $Parameters       =     array();
        
        //====================================================================//
        //  Load Locales Parameters
        if ($this->container->hasParameter("locales")) {
            $Parameters["Langs"] = $this->container->getParameter("locales");
        } else {
            $Parameters["Langs"] = array($this->container->getParameter("locale"));
        }
        
        return $Parameters;        
//        $this->config       =   $this->container->getParameter("splash");

dump($this->container->getParameter("locale")); 
dump($this->container->hasParameter("locales")); 
exit;
        //====================================================================//
        // Load Recurent Use Parameters
        $multilang    =   Mage::getStoreConfig('splashsync_splash_options/langs/multilang');
        $default_lang =   Mage::getStoreConfig('splashsync_splash_options/langs/default_lang');
        

        //====================================================================//
        // Server Actives Languages List
        $Parameters["Langs"] = array();
        
        //====================================================================//
        // Single Language Mode
        if ( empty($multilang) && !empty($default_lang) ) {
            $Parameters["Langs"][] = Mage::getStoreConfig('splashsync_splash_options/langs/default_lang');
        } 
        
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
    public function Objects()
    {
        //====================================================================//
        // Load Objects Type List
        return $this->Object()->getAnnotationManager()->getObjectsTypes();
    }   

    /**
     *      @abstract   Get Specific Object Class
     *                  This function is a router for all local object classes & functions
     * 
     *      @params     $type       Specify Object Class Name
     * 
     *      @return     OsWs_LinkerCore
     */
    public function Object($ObjectType = Null)
    {    
        //====================================================================//
        // Check in Cache
        $Index = (is_null($ObjectType) ? "__CORE__" : $ObjectType);
        if (array_key_exists( $Index , $this->objects ) ) {
            return $this->objects[$Index];
        }
        
        //====================================================================//
        // Init Annotations Manager
        if (is_null($this->_am)) {
            $EntityManager  =   $DocumentManager  = Null;
            //====================================================================//
            // Load Doctrine Entity Manager
            if ($this->getParameter("use_doctrine")) {
                $EntityManager  =   $this->container->get("doctrine")->getManager();
            }
            //====================================================================//
            // Load Doctrine Documents Manager
            if ($this->getParameter("use_doctrine_mongodb")) {
                $DocumentManager  =   $this->container->get("doctrine_mongodb")->getManager();
            }
            //====================================================================//
            // Create Annotations Manager
            $this->_am = new Annotations($EntityManager,$DocumentManager,$this->getParameter("objects"));
        }    
        //====================================================================//
        // Initialize Local Object Manager
        $this->objects[$Index] = new ObjectsManager($this->_am, $this->container, $ObjectType);        
        
        return $this->objects[$Index];
    }

    /**
     *      @abstract   Build list of Available Widgets
     * 
     *      @return     array       $list           list array including all available Widgets Type 
     */
    public function Widgets()
    {
        //====================================================================//
        // Init Annotations Manager
        if (is_null($this->_wm)) {
            //====================================================================//
            // Create Annotations Manager
            $this->_wm = new WidgetAnnotations($this->getParameter("widgets"));
        }  
        
        //====================================================================//
        // Load Widgets Type List
        return $this->_wm->getWidgetsTypes();
    }   

    /**
     *      @abstract   Get Specific Widgets Class
     *                  This function is a router for all local Widgets classes & functions
     * 
     *      @params     $type       Specify Widgets Class Name
     * 
     *      @return     OsWs_LinkerCore
     */
    public function Widget($WidgetType = Null)
    {    
        //====================================================================//
        // Check in Cache
        $Index = (is_null($WidgetType) ? "__CORE__" : $WidgetType);
        if (array_key_exists( $Index , $this->widgets ) ) {
            return $this->widgets[$Index];
        }
        
        //====================================================================//
        // Init Annotations Manager
        if (is_null($this->_wm)) {
            //====================================================================//
            // Create Annotations Manager
            $this->_wm = new WidgetAnnotations($this->getParameter("widgets"));
        }
        
        //====================================================================//
        // Initialize Local Widget Annotation
        $this->widgets[$Index] = $this->_wm->getAnnotations($WidgetType);
        //====================================================================//
        // Setup Local Widget Annotation
        if ($this->widgets[$Index]) {
            $this->widgets[$Index]->setContainer($this->container);
        }
        
        return $this->widgets[$Index];
    }

    
//====================================================================//
// *******************************************************************//
// Place Here Any SPECIFIC or COMMON Local Functions
// *******************************************************************//
//====================================================================//
    
    /**
     *      @abstract       Initiate Local Request User if not already defined
     *      @param          array       $cfg       Loacal Parameters Array
     *      @return         int                     0 if KO, >0 if OK
     */
    public function LoadLocalUser()
    {
        global $conf,$db,$user;
        
        //====================================================================//
        // CHECK USER ALREADY LOADED
        //====================================================================//
        if ( isset($user->id) && !empty($user->id) )
        {
            return True;
        }
        
        //====================================================================//
        // LOAD USER FROM DATABASE
        //====================================================================//
        
        //====================================================================//
        // Include Object Dolibarr Class
        require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

        //====================================================================//
        // Read Local Configuration
        $userId = isset($conf->global->SPLASH_USER)?$conf->global->SPLASH_USER:Null;
        if ( empty($userId) ) {
            return Splash::Log()->Err("Local - Dolibarr Error : No Local User Defined.");
        }
        //====================================================================//
        // Load Local User

        $user = new User($db);
        if ($user->fetch($userId) != 1) {
            Splash::Log()->Err("Local : Unable to Load Local User");
            return Splash::Log()->Err("Local - Dolibarr Error : " . $user->error );
        }
        
        //====================================================================//
        // Load Local User Rights
        if (!$user->all_permissions_are_loaded) {
            $user->getrights(); 
        }
    }
    
    /**
     *      @abstract       Initiate Local Request User if not already defined
     *      @param          array       $cfg       Loacal Parameters Array
     *      @return         int                     0 if KO, >0 if OK
     */
    public function LoadDefaultLanguage()
    {
        global $langs;
        //====================================================================//
        // Load Default Language
        //====================================================================//
        if ( !empty(Splash::Configuration()->DefaultLanguage) ) {
            $langs->setDefaultLang(Splash::Configuration()->DefaultLanguage);
        }
    }
    
//====================================================================//
//  VARIOUS LOW LEVEL FUNCTIONS
//====================================================================//

    /**
     *  @abstract       Local Splahs Module Initialisation
     * 
     *      @param      string  $Key      Global Parameter Key
     *      @param      string  $Default  Default Parameter Value
     * 
     *      @return     string
     */
    public function Boot(ContainerInterface $container) 
    {
        //====================================================================//
        //  Store Container
        $this->container    =   $container;
        
        //====================================================================//
        //  Load Server Parameters
        $this->config       =   $this->container->getParameter("splash");
        
        unset(Splash::Core()->conf);
    }
    
    /**
     *      @abstract       Safe Get of A Global Parameter
     * 
     *      @param      string  $Key      Global Parameter Key
     *      @param      string  $Default  Default Parameter Value
     *      @param      string  $Domain   Parameters Domain Key
     * 
     *      @return     string
     */
    private function getParameter($Key, $Default = Null, $Domain = Null) 
    {
        if ($Domain) {
            return isset($this->config[$Domain][$Key])  ? $this->config[$Domain][$Key] : $Default;
        } 
        return isset($this->config[$Key])  ? $this->config[$Key] : $Default;
    }

    /**
     *      @abstract       Load Object Transformer Service for Container
     * 
     *      @param      string  $ServiceName      Transformer Service Name
     * 
     *      @return     mixed
     */
    public function getTransformer($ServiceName) 
    {
        //====================================================================//
        //  Safety Check - Container Initialized
        if (!$this->container) {
            return Null;
        } 
        //====================================================================//
        //  Safety Check - Requested Service Exists
        if (!$this->container->has($ServiceName)) {
            Splash::Log()->Err("Local : Unknown Local Service => " . $ServiceName);
            return Null;
        } 
        //====================================================================//
        //  Return Transformer Service
        $Transformer    =   $this->container->get($ServiceName);
        if (!is_a($Transformer, "Splash\Local\Objects\Transformer")) {
            Splash::Log()->Err("Local : Transformer Service Must Extends \Splash\Local\Objects\Transformer");
            return Null;
        } 
        return $Transformer;
    }

    
    /**
     * @abstract    Detect Object Type from Object Local Class
     *              This function is only used internaly to identify if an object is Mapped or Not for Splash
     *       
     * @param   string      $ClassName      Local Object Class Name
     * 
     * @return  string      $ObjectType     Local Object Splash Type Name or Null if not Mapped 
     */
    public function getObjectType($ClassName)
    {
        //====================================================================//
        // Load Objects Class List
        return $this->Object()->getAnnotationManager()->getObjectType($ClassName);
    } 
    
}

?>
