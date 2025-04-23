<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) Splash Sync  <www.splashsync.com>
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
use Splash\Bundle\Interfaces\Connectors\PrimaryKeysInterface;
use Splash\Bundle\Models\AbstractConnector;
use Splash\Bundle\Models\AbstractStandaloneObject;
use Splash\Bundle\Models\AbstractStandaloneWidget;
use Splash\Core\Client\Splash;
use Splash\Core\Components\ExtensionsManager;
use Splash\Core\Interfaces\FileProviderInterface;
use Splash\Core\Interfaces\Extensions\ObjectExtensionInterface;
use Splash\Core\Interfaces\Object\PrimaryKeysAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Standalone Generic Communication Connectors
 */
final class Standalone extends AbstractConnector implements FileProviderInterface, PrimaryKeysInterface
{
    // TODO : Remove This if unused
    use ContainerAwareTrait;

    const NAME = 'standalone';

    /**
     * @var array
     */
    private array $taggedObjects = array();

    /**
     * @var array
     */
    private array $taggedWidgets = array();

    /**
     * @var array
     */
    private array $taggedActions = array();

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
        // Company Information
        $response->company = $this->getParameter('company', '...', 'infos');
        $response->address = $this->getParameter('address', '...', 'infos');
        $response->zip = $this->getParameter('zip', '...', 'infos');
        $response->town = $this->getParameter('town', '...', 'infos');
        $response->country = $this->getParameter('country', '...', 'infos');
        $response->www = $this->getParameter('www', '...', 'infos');
        $response->email = $this->getParameter('email', '...', 'infos');
        $response->phone = $this->getParameter('phone', '...', 'infos');

        //====================================================================//
        // Server Icon
        /** @var string $icoPath */
        $icoPath = $this->getParameter('ico', '...', 'infos');
        $response->icoraw = Splash::File()->ReadFileContents(
            is_file($icoPath) ? $icoPath : (dirname(__DIR__).'/Resources/public/symfony_ico.png')
        );

        //====================================================================//
        // Server Logo & Images
        /** @var string $logoPath */
        $logoPath = $this->getParameter('ico', '...', 'infos');
        if ($logoPath) {
            $response->logourl = (0 === strpos($logoPath, 'http'))
                    ? null
                    : filter_input(INPUT_SERVER, 'REQUEST_SCHEME').'://'.filter_input(INPUT_SERVER, 'SERVER_NAME');
            $response->logourl .= $this->getParameter('logo', null, 'infos');
        } else {
            $response->logourl = 'http://symfony.com/logos/symfony_black_03.png?v=5';
        }

        //====================================================================//
        // Server Information
        $response->servertype = 'Symfony PHP Framework';
        $response->serverurl = filter_input(INPUT_SERVER, 'SERVER_NAME')
            ?: 'localhost:8000'
        ;

        return $response;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
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
        // Return Self-test Result
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
        try {
            return $this->getObjectService($objectType)->description();
        } catch (Exception $e) {
            Splash::log()->report($e);

            return array();
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function getObjectFields(string $objectType): array
    {
        return $this->getObjectService($objectType)->fields();
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function getObjectList(string $objectType, string $filter = null, array $params = array()): array
    {
        return $this->getObjectService($objectType)->objectsList($filter, $params);
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function getObjectIdByPrimary(string $objectType, array $keys): ?string
    {
        $service = $this->getObjectService($objectType);
        //====================================================================//
        // Check Object Service
        if ($service instanceof PrimaryKeysAwareInterface) {
            //====================================================================//
            // Forward Action
            return $service->getByPrimary($keys) ?: null;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function getObject(string $objectType, $objectIds, array $fieldsList): ?array
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
     *
     * @throws Exception
     */
    public function setObject(string $objectType, string $objectId = null, array $data = array()): ?string
    {
        return $this->getObjectService($objectType)->set($objectId, $data);
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
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
    public function getFile(string $filePath, string $fileMd5): ?array
    {
        //====================================================================//
        // Load File Using Core Methods
        return Splash::file()->getFile($filePath, $fileMd5);
    }

    //====================================================================//
    // File Provider Interfaces
    //====================================================================//

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function hasFile(string $file, string $md5): bool
    {
        //====================================================================//
        //  Walk on Connector Files
        foreach ($this->getAvailableObjects() as $objectType) {
            //====================================================================//
            // Check if Object Service is a File Provider
            $objectService = $this->getObjectService($objectType);
            if (!($objectService instanceof FileProviderInterface)) {
                continue;
            }
            //====================================================================//
            //  CHECK IF FILE IS AVAILABLE
            if ($objectService->hasFile($file, $md5)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function readFile(string $file, string $md5): ?array
    {
        //====================================================================//
        //  Walk on Connector Files
        foreach ($this->getAvailableObjects() as $objectType) {
            //====================================================================//
            // Check if Object Service is a File Provider
            $objectService = $this->getObjectService($objectType);
            if (!($objectService instanceof FileProviderInterface)) {
                continue;
            }
            //====================================================================//
            //  CHECK IF FILE IS AVAILABLE
            $fileArray = $objectService->readFile($file, $md5);
            if (is_array($fileArray)) {
                return $fileArray;
            }
        }

        return null;
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
     *
     * @throws Exception
     */
    public function getWidgetDescription(string $widgetType): array
    {
        return $this->getWidgetService($widgetType)->description();
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function getWidgetContents(string $widgetType, array $params = array()): ?array
    {
        return $this->getWidgetService($widgetType)->get($params);
    }

    //====================================================================//
    // Profile Interfaces
    //====================================================================//

    /**
     * Get Connector Profile Information
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
     * @throws Exception
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
    public function getMasterAction(): ?string
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
     * Register a Tagged Standalone Object Extension
     *
     * @param ObjectExtensionInterface $objectExtension
     *
     * @return void
     */
    public function registerObjectExtension(ObjectExtensionInterface $objectExtension): void
    {
        ExtensionsManager::addObjectExtension($objectExtension);
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
