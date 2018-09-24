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


namespace Connectors\CoreBundle\Models;

use Nodes\CoreBundle\Repository\NodeRepository;
use Symfony\Bridge\Monolog\Logger;
use Nodes\StatsBundle\Services\NodesStatsService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Connectors\CoreBundle\Traits\NodeTrait;
use Connectors\CoreBundle\Traits\TasksTrait;
use Connectors\CoreBundle\Traits\LogsTrait;
use Connectors\CoreBundle\Traits\ContextTrait;
use Connectors\CoreBundle\Traits\XmlEncoderTrait;
use Connectors\CoreBundle\Traits\MessagesEncoderTrait;
use Connectors\CoreBundle\Traits\StatisticsTrait;
use Connectors\CoreBundle\Traits\NotificationsTrait;

use Application\MongoStatsBundle\Traits\MongoStatsAware;

/**
 * @abstract Base Class for All Nodes Communication Connectors 
 */
abstract class AbstractConnector implements ConnectorInterface {

    const ENABLE_DEBUG      = False;            //  Enable Connectors Transaction Debugging
    
    const TYPE_SERVER       = "Server";
    const TYPE_ACCOUNT      = "Account";
    const TYPE_HIDDEN       = "Hidden";
    
    static $Default = array(
        'enabled'   =>      True,
        'beta'      =>      True,
        'type'      =>      Null,
        'name'      =>      '',
        'connector' =>      '',
        'title'     =>      '',
        'label'     =>      '',
        'domain'    =>      False,
        'ico'       =>      '/bundles/theme/img/Splash-ico.png',
        'www'       =>      'www.splashsync.com',
    );
    
    use NodeTrait;
    use TasksTrait;
    use LogsTrait;
    use ContextTrait;
    use XmlEncoderTrait;
    use MessagesEncoderTrait;
    use StatisticsTrait;
    use NotificationsTrait;
    use MongoStatsAware;
    
    /**
     * @abstract    Logger
     * @var         Logger
     */
    private  $Logger;
    
    /*
     * @abstract Node Repository
     */
    private $NodesRepository;

    public function __construct(NodeRepository $NodesRepository, Logger $Logger, EventDispatcherInterface $Dispatcher) {
        $this->NodesRepository      =   $NodesRepository;
        $this->Logger               =   $Logger;
        $this->setEventDispatcher($Dispatcher);
        $this->IntXmlParser();
    }      
    
    /**
     * @abstract    Access Logger
     * @return      \Symfony\Bridge\Monolog\Logger
     */    
    public function getLogger()
    {
        return $this->Logger;
    }  
    
    /**
     * @abstract    Reset All Connector Objects before New Transaction
     * @return      self
     */    
    public function reset()
    {
        $this
                ->cleanNode()               //  Unset Current Node from Connector
                ->cleanContext()            //  Clean Connector Context Data
                ->cleanMongoStatsUser()     //  Clean Mongo Stats Current User
                ->cleanTasks()              //  Empty OutGoing Tasks Buffer
                ->cleanTasksResults()       //  Empty Tasks Results Buffer
                ->cleanFault()              //  Clean Faults  
                ->cleanLogs()               //  Clean Logs
                ;
        return $this;
    }  
}
