<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
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
use Splash\Bundle\Form\StandaloneFormType;
use Splash\Bundle\Models\AbstractConnector;
use Splash\Bundle\Models\AbstractStandaloneObject;
use Splash\Bundle\Models\AbstractStandaloneWidget;
use Splash\Client\Splash;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Standalone Generic Communication Connectors
 */
final class Standalone extends AbstractConnector
{
    use ContainerAwareTrait;

    const NAME = 'standalone';

    /**
     * @var array
     */
    private $taggedObjects = array();

    /**
     * @var array
     */
    private $taggedWidgets = array();

    /**
     * @var array
     */
    private $taggedActions = array();

    /**
     * {@inheritdoc}
     */
    public function ping(): bool
    {
        return Splash::ping();
    }

    /**
     * {@inheritdoc}
     */
    public function connect(): bool
    {
        return Splash::connect();
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
        return array_keys($this->taggedObjects);
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
        //====================================================================//
        // Single Object Reading
        if (is_scalar($objectIds)) {
            return $this->getObjectService($objectType)->get((string) $objectIds, $fieldsList);
        }
        //====================================================================//
        // Multiple Objects Reading
        $data = array();
        foreach ($objectIds as $objectId) {
            $data[$objectId] = $this->getObjectService($objectType)->get((string) $objectId, $fieldsList);
        }

        return $data;
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
        return array_keys($this->taggedWidgets);
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDescription(string $widgetType): array
    {
        return $this->getWidgetService($widgetType)->description();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetContents(string $widgetType, array $widgetParams = array())
    {
        return $this->getWidgetService($widgetType)->get($widgetParams);
    }

    //====================================================================//
    // Profile Interfaces
    //====================================================================//

    /**
     * Get Connector Profile Informations
     *
     * @return array
     */
    public function getProfile(): array
    {
        return array(
            'enabled' => true,                                  // is Connector Enabled
            'beta' => true,                                     // is this a Beta release
            'type' => self::TYPE_CLIENT,                        // Connector Type or Mode
            'name' => self::NAME,                               // Connector code (lowercase, no space allowed)
            'connector' => 'splash.connectors.standalone',      // Connector PUBLIC service
            'title' => 'Symfony Standalone Connector',          // Public short name
            'label' => 'Standalone Connector '.'for All Symfony Applications',  // Public long name
            'domain' => false,                                  // Translation domain for names
            'ico' => 'bundles/splash/splash-ico.png',           // Public Icon path
            'www' => 'www.splashsync.com',                      // Website Url
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
     * Collect List of Objects & Widgets Templates for Profiles Rendering
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
     * Register a Tagged Standalone Action
     *
     * @param string $actionCode
     * @param string $actionController
     *
     * @return void
     */
    public function registerStandaloneAction(string $actionCode, string $actionController): void
    {
        $this->taggedActions[$actionCode] = $actionController;
    }

    /**
     * No Master Action for Standalone Connectors
     * {@inheritdoc}
     */
    public function getMasterAction()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicActions(): array
    {
        return $this->taggedActions;
    }

    /**
     * No Secured Action for Standalone Connectors
     * Use internal routes instead
     *
     * {@inheritdoc}
     */
    public function getSecuredActions(): array
    {
        return array();
    }

    //====================================================================//
    // Objects Interfaces
    //====================================================================//

    /**
     * Register a Tagged Standalone Object Service
     *
     * @param string                   $objectType
     * @param AbstractStandaloneObject $objectService
     *
     * @return void
     */
    public function registerObjectService(string $objectType, AbstractStandaloneObject $objectService): void
    {
        $this->taggedObjects[$objectType] = $objectService;
    }

    /**
     * Get Configured to Standalone Object Service
     *
     * @param string $objectType
     *
     * @throws Exception
     *
     * @return AbstractStandaloneObject
     */
    public function getObjectService(string $objectType): AbstractStandaloneObject
    {
        //====================================================================//
        // Safety Check
        if (!isset($this->taggedObjects[$objectType])) {
            throw new Exception(sprintf(
                'Standalone Object  "%s" is not defined. Did you tag your service as  "splash.standalone.object"?',
                $objectType
            ));
        }
        //====================================================================//
        // Configure Object Service
        $this->taggedObjects[$objectType]->configure($objectType, $this->getWebserviceId(), $this->getConfiguration());
        //====================================================================//
        // Connect to Object Service
        return $this->taggedObjects[$objectType];
    }

    //====================================================================//
    // Widgets Interfaces
    //====================================================================//

    /**
     * Register a Tagged Standalone Widget Service
     *
     * @param string                   $widgetType
     * @param AbstractStandaloneWidget $widgetService
     *
     * @return void
     */
    public function registerWidgetService(string $widgetType, AbstractStandaloneWidget $widgetService): void
    {
        $this->taggedWidgets[$widgetType] = $widgetService;
    }

    /**
     * Get Configured to Standalone Object Service
     *
     * @param string $widgetType
     *
     * @throws Exception
     *
     * @return AbstractStandaloneWidget
     */
    public function getWidgetService(string $widgetType): AbstractStandaloneWidget
    {
        //====================================================================//
        // Safety Check
        if (!isset($this->taggedWidgets[$widgetType])) {
            throw new Exception(sprintf(
                'Standalone Widget  "%s" is not defined. Did you tag your service as  "splash.standalone.widget"?',
                $widgetType
            ));
        }
        //====================================================================//
        // Configure Widget Service
        $this->taggedWidgets[$widgetType]->configure($widgetType, $this->getWebserviceId(), $this->getConfiguration());
        //====================================================================//
        // Connect to Widget Service
        return $this->taggedWidgets[$widgetType];
    }
}
