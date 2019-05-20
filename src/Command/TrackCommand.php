<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2019 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Command;

use Splash\Bundle\Interfaces\Connectors\TrackingInterface;
use Splash\Bundle\Models\AbstractCommand;
use Splash\Client\Splash;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Symfony Console Command to Track Objects Changes on a Given Node.
 */
class TrackCommand extends AbstractCommand
{
    /**
     * Configure repair Command.
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('splash:objects:track')
            ->setDescription('Splash Connector : Track & Commit Objects Changes for a Given Node')
        ;
    }

    /**
     * Execute Console Command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //==============================================================================
        // Use Sf Event to Identify Server
        $this->identify($input);
        //==============================================================================
        // Render Connector Basic Infos
        if ($output->isVerbose()) {
            $this->showConfiguration($output);
        }
        //====================================================================//
        // Safety Check => Verify Selftest Pass
        if (!$this->connector->selfTest()) {
            $this->showLogs($output, false);

            return;
        }
        //====================================================================//
        // Safety Check => Verify Selftest Pass
        if (!$this->connector->isTrackingConnector() && is_subclass_of($this->connector, TrackingInterface::class)) {
            $output->writeln("This Connector is Not Tracking Object Changes");

            return;
        }
        //==============================================================================
        // Walk on Connector Objects
        $output->writeln('<info>------------------------------------------------------</info>');
        foreach ($this->connector->getAvailableObjects() as $objectType) {
            //==============================================================================
            // Check if this Object Type Tracks Changes
            if (!$this->connector->isObjectTracked($objectType)) {
                continue;
            }
            //==============================================================================
            // Commit Changes
            $commited = $this->connector->doObjectChangesTracking($objectType);
            $output->writeln('  '.$objectType.': '.$commited.' Change(s) Commited.');
        }
        $output->writeln('<info>------------------------------------------------------</info>');

        $this->showLogs($output, true);
    }
}
