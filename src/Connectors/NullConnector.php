<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2020 Splash Sync  <www.splashsync.com>
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
use Splash\Bundle\Models\AbstractConnector;
use Splash\Client\Splash;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Null Connector for Splash Bundle
 */
final class NullConnector extends AbstractConnector
{
    const NAME = 'nullConnector';

    /**
     * {@inheritdoc}
     */
    public function ping(): bool
    {
        Splash::log()->war('Null Connector Ping Always Pass...');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function connect(): bool
    {
        Splash::log()->war('Null Connector Connect Always Pass...');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function informations(ArrayObject  $informations): ArrayObject
    {
        //====================================================================//
        // Init Response Object
        $response = $informations;
        //====================================================================//
        // Server Logo & Images
        $response->icoraw = Splash::File()->ReadFileContents(
            (dirname(__DIR__).'/Resources/public/symfony_ico.png')
        );
        $response->logourl = 'http://symfony.com/logos/symfony_black_03.png?v=5';
        //====================================================================//
        // Server Informations
        $response->servertype = 'Null Connector';
        $response->serverurl = filter_input(INPUT_SERVER, 'SERVER_NAME')
                ? filter_input(INPUT_SERVER, 'SERVER_NAME')
                : 'localhost:8000';
        //====================================================================//
        // Module Informations
        $response->moduleauthor = "Splash Sync";
        $response->moduleversion = "dev-master";

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function selfTest(): bool
    {
        Splash::log()->war('Null Connector SelfTest Always Pass...');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableObjects(): array
    {
        return array();
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getObjectDescription(string $objectType): array
    {
        return array();
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getObjectFields(string $objectType): array
    {
        return array();
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getObjectList(string $objectType, string $filter = null, array $params = array()): array
    {
        return array("meta" => array("current" => 0, "total" => 0));
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getObject(string $objectType, $objectIds, array $fieldsList)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setObject(string $objectType, string $objectId = null, array $data = array())
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function deleteObject(string $objectType, string $objectId): bool
    {
        return false;
    }

    //====================================================================//
    // Files Interfaces
    //====================================================================//

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getFile(string $filePath, string $fileMd5)
    {
        return false;
    }

    //====================================================================//
    // Widgets Interfaces
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    public function getAvailableWidgets(): array
    {
        return array();
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getWidgetDescription(string $widgetType): array
    {
        return array();
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getWidgetContents(string $widgetType, array $widgetParams = array())
    {
        return array();
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
            'enabled' => false,                                 // Is Connector Enabled
            'beta' => true,                                     // Is this a Beta release
            'type' => self::TYPE_HIDDEN,                        // Connector Type or Mode
            'name' => self::NAME,                               // Connector code (lowercase, no space allowed)
            'connector' => 'splash.connectors.null',            // Connector PUBLIC service
            'title' => 'Null Connector',                        // Public short name
            'label' => 'Null Connector for Various Usages',     // Public long name
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
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getOfflineTemplate(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getNewTemplate(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getFormBuilderName(): string
    {
        return TextType::class;
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
        return array();
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
}
