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

use Splash\Bundle\Models\ConnectorInterface;

use Splash\Models\AbstractObject;
//use Nodes\CoreBundle\Repository\NodeRepository;
//use Symfony\Bridge\Monolog\Logger;
//use Nodes\StatsBundle\Services\NodesStatsService;
//use Symfony\Component\EventDispatcher\EventDispatcherInterface;

//use Connectors\CoreBundle\Traits\NodeTrait;
//use Connectors\CoreBundle\Traits\TasksTrait;
//use Connectors\CoreBundle\Traits\LogsTrait;
//use Connectors\CoreBundle\Traits\ContextTrait;
//use Connectors\CoreBundle\Traits\XmlEncoderTrait;
//use Connectors\CoreBundle\Traits\MessagesEncoderTrait;
//use Connectors\CoreBundle\Traits\StatisticsTrait;
//use Connectors\CoreBundle\Traits\NotificationsTrait;
//
//use Application\MongoStatsBundle\Traits\MongoStatsAware;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Splash\Bundle\Events\ObjectsListingEvent;
use Splash\Bundle\Traits\ConfigurationAwareTrait;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * @abstract Standalone Generic Communication Connectors 
 */
class Standalone implements ConnectorInterface {

    use ConfigurationAwareTrait;
    use ContainerAwareTrait;
    
    /**
     * @var     EventDispatcherInterface
     */
    private  $Dispatcher;
    
    public function __construct(EventDispatcherInterface $Dispatcher) {
        $this->Dispatcher      =   $Dispatcher;
    }      
    
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
     */
    public function informations(ArrayObject  $Informations) : ArrayObject
    {
        //====================================================================//
        // Init Response Object
        $Response = $Informations;
        
        //====================================================================//
        // Company Informations
        $Response->company          =   $this->getParameter("company",  "...", "infos");
        $Response->address          =   $this->getParameter("address",  "...", "infos");
        $Response->zip              =   $this->getParameter("zip",      "...", "infos");
        $Response->town             =   $this->getParameter("town",     "...", "infos");
        $Response->country          =   $this->getParameter("country",  "...", "infos");
        $Response->www              =   $this->getParameter("www",      "...", "infos");
        $Response->email            =   $this->getParameter("email",    "...", "infos");
        $Response->phone            =   $this->getParameter("phone",    "...", "infos");
        
        //====================================================================//
        // Server Logo & Images
        $icopath = $this->getParameter("ico",    "...", "infos");
        $Response->icoraw           =   Splash::File()->ReadFileContents(
                is_file($icopath) ? $icopath : (dirname(__DIR__) . "/Resources/public/symfony_ico.png")
                );

        if ($this->getParameter("logo", null, "infos")) {
            $Response->logourl      =   (strpos($this->getParameter("logo", null, "infos"), "http") === 0) ? null : filter_input(INPUT_SERVER, "REQUEST_SCHEME") . "://" . filter_input(INPUT_SERVER, "SERVER_NAME");
            $Response->logourl     .=   $this->getParameter("logo", null, "infos");
        } else {
            $Response->logourl          =   "http://symfony.com/logos/symfony_black_03.png?v=5";
        }
        
        //====================================================================//
        // Server Informations
        $Response->servertype       =   "Symfony PHP Framework";
        $Response->serverurl        =   filter_input(INPUT_SERVER, "SERVER_NAME") ? filter_input(INPUT_SERVER, "SERVER_NAME") : "localhost:8000";

//        //====================================================================//
//        // Module Informations
//        $Response->moduleauthor     =   SPLASH_AUTHOR;
//        $Response->moduleversion    =   SPLASH_VERSION;        
        
        return $Response;
    }
            
    /**
     * @abstract   Fetch Server Parameters
     * @return  array
     */    
    public function parameters() : array
    {
        $Parameters       =     array();

        //====================================================================//
        // Server Identification Parameters
        $Parameters["WsIdentifier"]         =   $this->getParameter("id");
        $Parameters["WsEncryptionKey"]      =   $this->getParameter("key");
        
        //====================================================================//
        // If Expert Mode => Overide of Server Host Address
        if (!empty($this->getParameter("host"))) {
            $Parameters["WsHost"]           =   $this->getParameter("host");
        }
//        
//        //====================================================================//
//        // Use of Symfony Routes => Overide of Local Server Path Address
//        if ($this->getContainer()) {
//            $Parameters["ServerPath"]      =   $this->getContainer()->get('router')
//                    ->generate("splash_main_soap");
//        }
//        
//        //====================================================================//
//        // If no Server Name => We are in Command Mode
//        if ((Splash::Input("SCRIPT_NAME") === "app/console")
//            || (Splash::Input("SCRIPT_NAME") === "bin/console")) {
//            $Parameters["ServerHost"]      =   "localhost";
//        }
        
        return $Parameters;
    }        
        
    
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
    public function Objects() 
    {
        //====================================================================//
        // Dispatch Object Listing Event
        $Event  =   $this->Dispatcher->dispatch(ObjectsListingEvent::NAME, new ObjectsListingEvent());
        //====================================================================//
        // Return Objects Types Array
        return $Event->getObjectTypes();
    }
    
    /**
     * {@inheritdoc}
     */
    public function Object( string $ObjectType ) : AbstractObject
    {
        //====================================================================//
        // Dispatch Object Listing Event
        $Event  =   $this->Dispatcher->dispatch(ObjectsListingEvent::NAME, new ObjectsListingEvent());
        //====================================================================//
        // Load Object Service Name
        $ServiceName    =   $Event->getServiceName($ObjectType);
        //====================================================================//
        // Safety Check
        if ( empty($ServiceName) || !$this->container->has($ServiceName)) {
            throw new Exception("Unable to identify Object Service : " . $ServiceName);
        }      
        //====================================================================//
        // Connect to Object Service
        return $this->container->get($ServiceName);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAvailableObjects(array $Config)
    {
        //====================================================================//
        // Dispatch Object Listing Event
        $Event  =   $this->Dispatcher->dispatch(ObjectsListingEvent::NAME, new ObjectsListingEvent());
        //====================================================================//
        // Return Objects Types Array
        return $Event->getObjectTypes();
    }
    
    /**
     * {@inheritdoc}
     */    
    public function getObjectDescription(array $Config, string $ObjectType)
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
    
    /**
     * {@inheritdoc}
     */    
    public function getObject(array $Config, string $ObjectType, $Ids, array $List)
    {
        return $this->Object($ObjectType)->get($Ids, $List);
    }

    /**
     * {@inheritdoc}
     */    
    public function setObject(array $Config, string $ObjectType, $Id, array $Data)    
    {
        return $this->Object($ObjectType)->set($Id, $Data);
    }
    
    /**
     * @abstract   Get Connector Profile Informations
     * @return  array
     */    
    public function getProfile() : array
    {
        return array(
            'enabled'   =>      True,                                   // is Connector Enabled
            'beta'      =>      True,                                   // is this a Beta release
            'type'      =>      self::TYPE_SERVER,                      // Connector Type or Mode                
            'name'      =>      'standalone',                           // Connector code (lowercase, no space allowed) 
            'connector' =>      'splash.connectors.standalone',         // Connector PUBLIC service
            'title'     =>      'Symfony Standalone Connector',         // Public short name
            'label'     =>      'Standalone Connector '
            . 'for All Symfony Applications',                           // Public long name
            'domain'    =>      False,                                  // Translation domain for names
            'ico'       =>      'bundles/splash/splash-ico.png',        // Public Icon path
            'www'       =>      'www.splashsync.com',                   // Website Url
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function getProfileTemplate() : string
    {
        return true;
    }        
    
    /**
     * {@inheritdoc}
     */
    public function getFormBuilderName() : string
    {
        return true;
    }        

    /**
     * {@inheritdoc}
     */
    public function getAvailableActions() : ArrayObject
    {
        return new ArrayObject();
    }        
    
    /**
     * @abstract   Get Connector Profile on Listing
     * 
     * @return     string 
     */    
    public function onConnectorsListing(GenericEvent $Event)
    {
        $Event['Standalone'] =   array(
            'enabled'   =>      True,
            'beta'      =>      False,
            'type'      =>      self::TYPE_SERVER,
            'name'      =>      'Standalone',
            'connector' =>      'splash.connectors.standalone',  
            'title'     =>      'profile.card.title',
            'label'     =>      'profile.card.label',
            'domain'    =>      'SplashBundle',
            'ico'       =>      '/bundles/splash/img/MailJet-Icon.png',
        );
    }       
}
