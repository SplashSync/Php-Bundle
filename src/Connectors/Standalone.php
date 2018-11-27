<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2018 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Connectors;

use ArrayObject;
use Exception;
use Splash\Bundle\Events\Standalone\ActionsListingEvent;
use Splash\Bundle\Events\Standalone\ObjectsListingEvent;
use Splash\Bundle\Form\StandaloneFormType;
use Splash\Bundle\Models\AbstractConnector;
use Splash\Bundle\Models\AbstractStandaloneObject;
use Splash\Client\Splash;
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
    public function ping(): bool
    {
        Splash::log()->Msg('Standalone Connector Ping Always Pass');
        //====================================================================//
        // Ping is Ok by default
        $result = true;
        //====================================================================//
        // Execute Ping for All Objects
        foreach ($this->getAvailableObjects() as $objectType) {
            $objectService = $this->getObjectService($objectType);
            if (method_exists($objectService, 'ping')) {
                $result = $result && (bool) $objectService->ping();
            }
        }
        //====================================================================//
        // Return Ping Result
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function connect(): bool
    {
        Splash::log()->Msg('Standalone Connector Connect Always Pass');
        //====================================================================//
        // Connect is Ok by default
        $result = true;
        //====================================================================//
        // Execute Connect for All Objects
        foreach ($this->getAvailableObjects() as $objectType) {
            $objectService = $this->getObjectService($objectType);
            if (method_exists($objectService, 'connect')) {
                $result = $result && (bool) $objectService->connect();
            }
        }
        //====================================================================//
        // Return Connect Result
        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function informations(ArrayObject  $informations): ArrayObject
    {
        //====================================================================//
        // Init Response Object
        $response = $informations;

        //====================================================================//
        // Company Informations
        $response->company = $this->getParameter('company', '...', 'infos');
        $response->address = $this->getParameter('address', '...', 'infos');
        $response->zip = $this->getParameter('zip', '...', 'infos');
        $response->town = $this->getParameter('town', '...', 'infos');
        $response->country = $this->getParameter('country', '...', 'infos');
        $response->www = $this->getParameter('www', '...', 'infos');
        $response->email = $this->getParameter('email', '...', 'infos');
        $response->phone = $this->getParameter('phone', '...', 'infos');

        //====================================================================//
        // Server Logo & Images
        $icopath = $this->getParameter('ico', '...', 'infos');
        $response->icoraw = Splash::File()->ReadFileContents(
            is_file($icopath) ? $icopath : (dirname(__DIR__).'/Resources/public/symfony_ico.png')
        );

        if ($this->getParameter('logo', null, 'infos')) {
            $response->logourl = (0 === strpos($this->getParameter('logo', null, 'infos'), 'http'))
                    ? null
                    : filter_input(INPUT_SERVER, 'REQUEST_SCHEME').'://'.filter_input(INPUT_SERVER, 'SERVER_NAME');
            $response->logourl .= $this->getParameter('logo', null, 'infos');
        } else {
            $response->logourl = 'http://symfony.com/logos/symfony_black_03.png?v=5';
        }

        //====================================================================//
        // Server Informations
        $response->servertype = 'Symfony PHP Framework';
        $response->serverurl = filter_input(INPUT_SERVER, 'SERVER_NAME')
                ? filter_input(INPUT_SERVER, 'SERVER_NAME')
                : 'localhost:8000';

//        //====================================================================//
//        // Module Informations
//        $Response->moduleauthor     =   SPLASH_AUTHOR;
//        $Response->moduleversion    =   SPLASH_VERSION;

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function selfTest(): bool
    {
        Splash::log()->Msg('Standalone Connector SelfTest Always Pass');
        //====================================================================//
        // SelfTest is Ok by default
        $result = true;
        //====================================================================//
        // Execute SelfTest for All Objects
        foreach ($this->getAvailableObjects() as $objectType) {
            $objectService = $this->getObjectService($objectType);
            if (method_exists($objectService, 'selftest')) {
                $result = $result && (bool) $objectService->selftest();
            }
        }
        //====================================================================//
        // Return Selftest Result
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableObjects(): array
    {
        //====================================================================//
        // Dispatch Object Listing Event
        /** @var ObjectsListingEvent $event */
        $event = $this->getEventDispatcher()->dispatch(ObjectsListingEvent::NAME, new ObjectsListingEvent());
        //====================================================================//
        // Return Objects Types Array
        return $event->getObjectTypes();
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectDescription(string $objectType): array
    {
        return $this->getObjectService($objectType)->description();
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectFields(string $objectType): array
    {
        return $this->getObjectService($objectType)->fields();
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectList(string $objectType, string $filter = null, array $params = array()): array
    {
        return $this->getObjectService($objectType)->objectsList($filter, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function getObject(string $objectType, $objectIds, array $fieldsList)
    {
        return $this->getObjectService($objectType)->get($objectIds, $fieldsList);
    }

    /**
     * {@inheritdoc}
     */
    public function setObject(string $objectType, string $objectId = null, array $data = array())
    {
        return $this->getObjectService($objectType)->set($objectId, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteObject(string $objectType, string $objectId): bool
    {
        return $this->getObjectService($objectType)->delete($objectId);
    }

    //====================================================================//
    // Files Interfaces
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    public function getFile(string $filePath, string $fileMd5)
    {
        //====================================================================//
        // Load File Using Core Methods
        return Splash::file()->getFile($filePath, $fileMd5);
    }

    //====================================================================//
    // Widgets Interfaces
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    public function getAvailableWidgets(): array
    {
        return array('SelfTest');
        //====================================================================//
        // Dispatch Object Listing Event
        /** @var ObjectsListingEvent $event */
        $event = $this->getEventDispatcher()->dispatch(ObjectsListingEvent::NAME, new ObjectsListingEvent());
        //====================================================================//
        // Return Objects Types Array
        return $event->getObjectTypes();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDescription(string $widgetType): array
    {
        return $this->getObjectService($widgetType)->description();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetContents(string $widgetType, array $widgetParams = array())
    {
        return array();
//        return $this->getObjectService($WidgetType)->get($Params);
    }

    //====================================================================//
    // Profile Interfaces
    //====================================================================//

    /**
     * @abstract   Get Connector Profile Informations
     *
     * @return array
     */
    public function getProfile(): array
    {
        return array(
            'enabled' => true,                                   // is Connector Enabled
            'beta' => true,                                   // is this a Beta release
            'type' => self::TYPE_SERVER,                      // Connector Type or Mode
            'name' => 'standalone',                           // Connector code (lowercase, no space allowed)
            'connector' => 'splash.connectors.standalone',         // Connector PUBLIC service
            'title' => 'Symfony Standalone Connector',         // Public short name
            'label' => 'Standalone Connector '.'for All Symfony Applications',                           // Public long name
            'domain' => false,                                  // Translation domain for names
            'ico' => 'bundles/splash/splash-ico.png',        // Public Icon path
            'www' => 'www.splashsync.com',                   // Website Url
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getConnectedTemplate(): string
    {
        return '@Splash/Standalone/connected.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getOfflineTemplate(): string
    {
        return '@Splash/Standalone/offline.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getNewTemplate(): string
    {
        return '@Splash/Standalone/new.html.twig';
    }

    /**
     * @abstract    Collect List of Objects & Widgets Templates for Profiles Rendering
     *
     * @param string $context Loading Context (New, Offline, Connected)
     *
     * @return array
     */
    public function getChildTemplates(string $context): array
    {
        Splash::log()->Deb('Loading Standalone Connector Templates for '.$context);
        $result = array();
        //====================================================================//
        // Safety Check
        if (!in_array($context, array('New', 'Offline', 'Connected'), true)) {
            return $result;
        }
        //====================================================================//
        // Load templates for All Objects
        foreach ($this->getAvailableObjects() as $objectType) {
            $objectService = $this->getObjectService($objectType);
            if (method_exists($objectService, 'get'.$context.'Template')) {
                $result[] = $objectService->{'get'.$context.'Template'}();
            }
        }
        //====================================================================//
        // Return Results
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormBuilderName(): string
    {
        return StandaloneFormType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableActions(): array
    {
        //====================================================================//
        // Dispatch Object Listing Event
        /** @var ActionsListingEvent $event */
        $event = $this->getEventDispatcher()->dispatch(ActionsListingEvent::NAME, new ActionsListingEvent());
        //====================================================================//
        // Return Actions Types Array
        return $event->getAll();
    }

    //====================================================================//
    // Objects Interfaces
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    private function getObjectService(string $objectType): AbstractStandaloneObject
    {
        //====================================================================//
        // Dispatch Object Listing Event
        /** @var ObjectsListingEvent $event */
        $event = $this->getEventDispatcher()->dispatch(ObjectsListingEvent::NAME, new ObjectsListingEvent());
        //====================================================================//
        // Load Object Service Name
        $serviceName = $event->getServiceName($objectType);
        //====================================================================//
        // Safety Check
        if (empty($serviceName) || !$this->container->has($serviceName)) {
            throw new Exception(sprintf('Unable to identify Object Service : %s', $serviceName));
        }
        //====================================================================//
        // Load Standalone Object Service
        $objetService = $this->container->get($serviceName);
        //====================================================================//
        // Safety Check
        if (!($objetService instanceof AbstractStandaloneObject)) {
            throw new Exception(sprintf("Object Service doesn't Extends %s", AbstractStandaloneObject::class));
        }
        //====================================================================//
        // Configure Object Service
        $objetService->configure($this->getWebserviceId(), $this->getConfiguration());
        //====================================================================//
        // Connect to Object Service
        return $objetService;
    }
}
