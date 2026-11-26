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

namespace Splash\Bundle\Models\Connectors\Standalone;

/**
 * Actions of the Standalone Connector: tagged controllers
 */
trait ActionsTrait
{
    /**
     * List of Tagged Actions Controllers
     *
     * @var array<string, string>
     */
    private array $taggedActions = array();

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
}
