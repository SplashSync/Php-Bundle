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

use Splash\Bundle\Events\ObjectsListingEvent;
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
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function connect() : bool
    {
        Splash::log()->Msg("Standalone Connector Connect Always Pass");
        Splash::log()->War("Standalone Connector Connect Always Pass");
        Splash::log()->Err("Standalone Connector Connect Always Pass");
        return true;
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
            
//    /**
//     * @abstract   Fetch Server Parameters
//     * @return  array
//     */
//    public function parameters() : array
//    {
//        $Parameters       =     array();
//
//        //====================================================================//
//        // Server Identification Parameters
//        $Parameters["WsIdentifier"]         =   $this->getParameter("id");
//        $Parameters["WsEncryptionKey"]      =   $this->getParameter("key");
//
//        //====================================================================//
//        // If Expert Mode => Overide of Server Host Address
//        if (!empty($this->getParameter("host"))) {
//            $Parameters["WsHost"]           =   $this->getParameter("host");
//        }
//
//        //====================================================================//
//        // Use of Symfony Routes => Overide of Local Server Path Address
//        if ($this->getContainer()) {
//            $Parameters["ServerPath"]      =   $this->R
//                    ->generate("splash_main_soap");
//        }
//
//        //====================================================================//
//        // If no Server Name => We are in Command Mode
//        if ((Splash::Input("SCRIPT_NAME") === "app/console")
//            || (Splash::Input("SCRIPT_NAME") === "bin/console")) {
//            $Parameters["ServerHost"]      =   "localhost";
//        }
//
//        return $Parameters;
//    }
        
    
    /**
     * {@inheritdoc}
     */
    public function selfTest() : bool
    {
        Splash::log()->Msg("Standalone Connector SelfTest Always Pass");
        return true;
    }
    
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
        if (!($ObjectType instanceof AbstractStandaloneObject)) {
            throw new Exception("Object Service doesn't Extends " . AbstractStandaloneObject::class);
        }
        //====================================================================//
        // Connect to Object Service
        return $this->container->get($ServiceName);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAvailableObjects()
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
        
        return $this->Object($ObjectType)->description();
    }
      
    /**
     * {@inheritdoc}
     */
    public function getObjectFields(array $Config, string $ObjectType)
    {
        return $this->Object($ObjectType)->fields();
    }
    
    public function getObjectList(array $Config, string $ObjectType, string $Filter = null, array $Params = [])
    {
        return $this->Object($ObjectType)->objectsList($Filter, $Params);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getObject(array $Config, string $ObjectType, $ObjectIds, array $List)
    {
        return $this->Object($ObjectType)->get($ObjectIds, $List);
    }

    /**
     * {@inheritdoc}
     */
    public function setObject(array $Config, string $ObjectType, $ObjectId, array $Data)
    {
        return $this->Object($ObjectType)->set($ObjectId, $Data);
    }
    
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
    public function getProfileTemplate() : string
    {
        return "@Splash/profile/profile.html.twig";
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
    public function getAvailableActions() : ArrayObject
    {
        return new ArrayObject();
    }
}
