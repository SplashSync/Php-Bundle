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

namespace   Splash\Bundle\Templates\Widgets;

use Splash\Bundle\Models\AbstractStandaloneWidget;
use Splash\Core\SplashCore      as Splash;

/**
 * SelfTest Template Widget for Splash Standalone Connector
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class SelfTestTemplate extends AbstractStandaloneWidget
{
    /**
     * Define Standard Options for this Widget
     *
     * @var array
     */
    public static $OPTIONS = array(
        'Width' => self::SIZE_DEFAULT,
        'UseCache' => true,
        'CacheLifeTime' => 1,
    );

    /**
     * {@inheritdoc}
     */
    protected static $NAME = 'Server SelfTest';

    /**
     * {@inheritdoc}
     */
    protected static $DESCRIPTION = 'Results of your Server SelfTests';

    /**
     * {@inheritdoc}
     */
    protected static $ICO = 'fa fa-info-circle';

    //====================================================================//
    // Class Main Functions
    //====================================================================//

    /**
     * Return Widget Customs Options
     *
     * @return array
     */
    public function options()
    {
        return self::$OPTIONS;
    }

    /**
     * Return requested Customer Data
     *
     * @param array $params Widget Inputs Parameters
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function get($params = null)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();

        //====================================================================//
        // Setup Widget Core Informations
        //====================================================================//

        $this->setTitle($this->getName());
        $this->setIcon($this->getIcon());

        //====================================================================//
        // Build Intro Text Block
        //====================================================================//
        $this->buildIntroBlock();

        //====================================================================//
        // Build Inputs Block
        //====================================================================//
        $this->buildNotificationsBlock();

        //====================================================================//
        // Set Blocks to Widget
        $blocks = $this->blocksFactory()->render();
        if ($blocks) {
            $this->setBlocks($blocks);
        }

        //====================================================================//
        // Publish Widget
        return $this->render();
    }

    //====================================================================//
    // Blocks Generation Functions
    //====================================================================//

    /**
     * Block Building - Text Intro
     */
    private function buildIntroBlock(): void
    {
        //====================================================================//
        // Into Text Block
        $this->blocksFactory()->addTextBlock('This widget show results of Local Server SelfTest');
    }

    /**
     * Block Building - Notifications Parameters
     */
    private function buildNotificationsBlock(): void
    {
        //====================================================================//
        // Execute Loacl SelfTest Function
        Splash::selfTest();
        //====================================================================//
        // Get Log
        $logs = Splash::log();
        //====================================================================//
        // If test was passed
        if (empty($logs->err)) {
            $this->blocksFactory()->addNotificationsBlock(array('success' => 'Self-Test Passed!'));
        }
        //====================================================================//
        // Add Error Notifications
        foreach ($logs->err as $message) {
            $this->blocksFactory()->addNotificationsBlock(array('error' => $message));
        }
        //====================================================================//
        // Add Warning Notifications
        foreach ($logs->war as $message) {
            $this->blocksFactory()->addNotificationsBlock(array('warning' => $message));
        }
        //====================================================================//
        // Add Success Notifications
        foreach ($logs->msg as $message) {
            $this->blocksFactory()->addNotificationsBlock(array('success' => $message));
        }
        //====================================================================//
        // Add Debug Notifications
        foreach ($logs->deb as $message) {
            $this->blocksFactory()->addNotificationsBlock(array('info' => $message));
        }
    }
}
