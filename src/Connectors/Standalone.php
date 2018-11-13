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
 **/


namespace Splash\Bundle\Connectors;

use ArrayObject;
use Exception;

use Splash\Client\Splash;

use Splash\Models\AbstractObject;

use Splash\Bundle\Events\Standalone\ObjectsListingEvent;
use Splash\Bundle\Events\Standalone\ActionsListingEvent;
use Splash\Bundle\Form\StandaloneFormType;
use Splash\Bundle\Models\AbstractConnector;
use Splash\Bundle\Models\AbstractStandaloneObject;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * @abstract Standalone Generic Communication Connectors
 */
final class Standalone extends AbstractConnector
{
    use ContainerAwareTrait;
    
    /**
     * {@inheritdoc}
     */
    public function ping() : bool
    {
        Splash::log()->Msg("Standalone Connector Ping Always Pass");
        //====================================================================//
        // Ping is Ok by default 
        $Result =   true;
        //====================================================================//
        // Execute Ping for All Objects 
        foreach ($this->getAvailableObjects() as $ObjectType) {
            $ObjectService  =   $this->getObjectService($ObjectType);
            if (method_exists($ObjectService, "ping")) {
                $Result &=  $ObjectService->ping();
            } 
        }
        //====================================================================//
        // Return Ping Result 
        return $Result;
    }

    /**
     * {@inheritdoc}
     */
    public function connect() : bool
    {
        Splash::log()->Msg("Standalone Connector Connect Always Pass");
        //====================================================================//
        // Connect is Ok by default 
        $Result =   true;
        //====================================================================//
        // Execute Connect for All Objects 
        foreach ($this->getAvailableObjects() as $ObjectType) {
            $ObjectService  =   $this->getObjectService($ObjectType);
            if (method_exists($ObjectService, "connect")) {
                $Result &=  $ObjectService->connect();
            } 
        }
        //====================================================================//
        // Return Connect Result 
        return $Result;
    }
        
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function informations(ArrayObject  $Informations) : ArrayObject
    {
        //====================================================================//
        // Init Response Object
        $Response = $Informations;
        
        //====================================================================//
        // Company Informations
        $Response->company          =   $this->getParameter("company", "...", "infos");
        $Response->address          =   $this->getParameter("address", "...", "infos");
        $Response->zip              =   $this->getParameter("zip", "...", "infos");
        $Response->town             =   $this->getParameter("town", "...", "infos");
        $Response->country          =   $this->getParameter("country", "...", "infos");
        $Response->www              =   $this->getParameter("www", "...", "infos");
        $Response->email            =   $this->getParameter("email", "...", "infos");
        $Response->phone            =   $this->getParameter("phone", "...", "infos");
        
        //====================================================================//
        // Server Logo & Images
        $icopath = $this->getParameter("ico", "...", "infos");
        $Response->icoraw           =   Splash::File()->ReadFileContents(
            is_file($icopath) ? $icopath : (dirname(__DIR__) . "/Resources/public/symfony_ico.png")
        );

        if ($this->getParameter("logo", null, "infos")) {
            $Response->logourl      =   (strpos($this->getParameter("logo", null, "infos"), "http") === 0)
                    ? null
                    : filter_input(INPUT_SERVER, "REQUEST_SCHEME") . "://" . filter_input(INPUT_SERVER, "SERVER_NAME");
            $Response->logourl     .=   $this->getParameter("logo", null, "infos");
        } else {
            $Response->logourl      =   "http://symfony.com/logos/symfony_black_03.png?v=5";
        }
        
        //====================================================================//
        // Server Informations
        $Response->servertype       =   "Symfony PHP Framework";
        $Response->serverurl        =   filter_input(INPUT_SERVER, "SERVER_NAME")
                ? filter_input(INPUT_SERVER, "SERVER_NAME")
                : "localhost:8000";

//        //====================================================================//
//        // Module Informations
//        $Response->moduleauthor     =   SPLASH_AUTHOR;
//        $Response->moduleversion    =   SPLASH_VERSION;
        
        return $Response;
    }
    
    /**
     * {@inheritdoc}
     */
    public function selfTest() : bool
    {
        Splash::log()->Msg("Standalone Connector SelfTest Always Pass");
        //====================================================================//
        // SelfTest is Ok by default 
        $Result =   true;
        //====================================================================//
        // Execute SelfTest for All Objects 
        foreach ($this->getAvailableObjects() as $ObjectType) {
            $ObjectService  =   $this->getObjectService($ObjectType);
            if (method_exists($ObjectService, "selftest")) {
                $Result &=  $ObjectService->selftest();
            } 
        }
        //====================================================================//
        // Return Selftest Result 
        return $Result;
    }
    
    //====================================================================//
    // Objects Interfaces
    //====================================================================//
    
    /**
     * {@inheritdoc}
     */
    private function getObjectService(string $ObjectType) : AbstractStandaloneObject
    {
        //====================================================================//
        // Dispatch Object Listing Event
        $Event  =   $this->getEventDispatcher()->dispatch(ObjectsListingEvent::NAME, new ObjectsListingEvent());
        //====================================================================//
        // Load Object Service Name
        $ServiceName    =   $Event->getServiceName($ObjectType);
        //====================================================================//
        // Safety Check
        if (empty($ServiceName) || !$this->container->has($ServiceName)) {
            throw new Exception("Unable to identify Object Service : " . $ServiceName);
        }
        //====================================================================//
        // Load Standalone Object Service
        $ObjetService   =   $this->container->get($ServiceName);
        //====================================================================//
        // Safety Check
        if (!($ObjetService instanceof AbstractStandaloneObject)) {
            throw new Exception("Object Service doesn't Extends " . AbstractStandaloneObject::class);
        }
        //====================================================================//
        // Configure Object Service
        $ObjetService->configure($this->getWebserviceId(), $this->getConfiguration());
        //====================================================================//
        // Connect to Object Service
        return $ObjetService;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAvailableObjects() : array
    {
        //====================================================================//
        // Dispatch Object Listing Event
        $Event  =   $this->getEventDispatcher()->dispatch(ObjectsListingEvent::NAME, new ObjectsListingEvent());
        //====================================================================//
        // Return Objects Types Array
        return $Event->getObjectTypes();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getObjectDescription(string $ObjectType)
    {
        
        return $this->getObjectService($ObjectType)->description();
    }
      
    /**
     * {@inheritdoc}
     */
    public function getObjectFields(string $ObjectType)
    {
        return $this->getObjectService($ObjectType)->fields();
    }
    
    public function getObjectList(string $ObjectType, string $Filter = null, array $Params = [])
    {
        return $this->getObjectService($ObjectType)->objectsList($Filter, $Params);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getObject(string $ObjectType, $ObjectIds, array $List)
    {
        return $this->getObjectService($ObjectType)->get($ObjectIds, $List);
    }

    /**
     * {@inheritdoc}
     */
    public function setObject(string $ObjectType, string $ObjectId = null, array $Data = array())
    {
        return $this->getObjectService($ObjectType)->set($ObjectId, $Data);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteObject(string $ObjectType, string $ObjectId)
    {
        return $this->getObjectService($ObjectType)->delete($ObjectId);
    }
    

    //====================================================================//
    // Files Interfaces
    //====================================================================//
    
    /**
     * {@inheritdoc}
     */   
    public function getFile(string $Path, string $Md5)
    {
        //====================================================================//
        // Load File Using Core Methods
        return Splash::file()->getFile($Path, $Md5);
    } 
    
    //====================================================================//
    // Widgets Interfaces
    //====================================================================//
    
    /**
     * {@inheritdoc}
     */
    public function getAvailableWidgets()
    {
        return array("SelfTest");
        //====================================================================//
        // Dispatch Object Listing Event
        $Event  =   $this->getEventDispatcher()->dispatch(ObjectsListingEvent::NAME, new ObjectsListingEvent());
        //====================================================================//
        // Return Objects Types Array
        return $Event->getObjectTypes();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetContents(string $ObjectType, $ObjectIds, array $List)
    {
        return $this->getObjectService($ObjectType)->get($ObjectIds, $List);
    }

    
    //====================================================================//
    // Profile Interfaces
    //====================================================================//
    
    /**
     * @abstract   Get Connector Profile Informations
     * @return  array
     */
    public function getProfile() : array
    {
        return array(
            'enabled'   =>      true,                                   // is Connector Enabled
            'beta'      =>      true,                                   // is this a Beta release
            'type'      =>      self::TYPE_SERVER,                      // Connector Type or Mode
            'name'      =>      'standalone',                           // Connector code (lowercase, no space allowed)
            'connector' =>      'splash.connectors.standalone',         // Connector PUBLIC service
            'title'     =>      'Symfony Standalone Connector',         // Public short name
            'label'     =>      'Standalone Connector '
            . 'for All Symfony Applications',                           // Public long name
            'domain'    =>      false,                                  // Translation domain for names
            'ico'       =>      'bundles/splash/splash-ico.png',        // Public Icon path
            'www'       =>      'www.splashsync.com',                   // Website Url
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function getConnectedTemplate() : string
    {
        return "@Splash/Standalone/connected.html.twig";
    }

    /**
     * {@inheritdoc}
     */
    public function getOfflineTemplate() : string
    {
        return "@Splash/Standalone/offline.html.twig";
    }

    /**
     * {@inheritdoc}
     */
    public function getNewTemplate() : string
    {
        return "@Splash/Standalone/new.html.twig";
    }
    
    /**
     * @abstract    Collect List of Objects & Widgets Templates for Profiles Rendering
     * @param   string  $Context    Loading Context (New, Offline, Connected)
     * @return  array
     */
    public function getChildTemplates(string $Context) : array
    {
        Splash::log()->Deb("Loading Standalone Connector Templates for " . $Context);
        $Result =   array();
        //====================================================================//
        // Safety Check
        if (!in_array($Context, ["New", "Offline", "Connected"])) {
            return $Result;        
        }
        //====================================================================//
        // Load templates for All Objects 
        foreach ($this->getAvailableObjects() as $ObjectType) {
            $ObjectService  =   $this->getObjectService($ObjectType);
            if (method_exists($ObjectService, "get" . $Context . "Template")) {
                $Result[] =  $ObjectService->{"get" . $Context . "Template"}();
            }
        }
        //====================================================================//
        // Return Results 
        return $Result;        
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFormBuilderName() : string
    {
        return StandaloneFormType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableActions() : array
    {
        //====================================================================//
        // Dispatch Object Listing Event
        $Event  =   $this->getEventDispatcher()->dispatch(ActionsListingEvent::NAME, new ActionsListingEvent());
        //====================================================================//
        // Return Actions Types Array
        return $Event->getAll();
    }
}
