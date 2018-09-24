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

use Splash\Bundle\Models\Connectors\ConnectorInterface;
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

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @abstract Standalone Generic Communication Connectors 
 */
class Standalone implements ConnectorInterface {

    
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
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function connect() : bool
    {
        return true;
    }
        
    /**
     * {@inheritdoc}
     */
    public function informations()
    {
        return true;
    }
            
    /**
     * {@inheritdoc}
     */
    public function selfTest() : bool
    {
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function Objects();
    
    /**
     * {@inheritdoc}
     */
    public function Object( string $ObjectType ) : AbstractObject;    
    
    
    /**
     * {@inheritdoc}
     */
    public function getProfileTemplate() : string;
    
    /**
     * {@inheritdoc}
     */
    public function getFormBuilderName() : string;

    /**
     * {@inheritdoc}
     */
    public function getAvailableActions() : ArrayObject;    
    
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
