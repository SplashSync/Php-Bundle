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

use Splash\Bundle\Models\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @abstract    Splash Ping Command
 */
class PingCommand extends AbstractCommand
{
    /**
     * Configure Symfony Command
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('splash:ping')
            ->setDescription('Splash : Perform Ping test')
        ;
    }

    /**
     * Execute Symfony Command
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
        // Perform Ping Test
        $result = $this->connector->ping();
        //====================================================================//
        // Output Result
        $output->writeln(
            $result
            ? "<bg=green;fg=white;options=bold>=== SPLASH : PING TEST PASSED </>"
            : "<bg=red;fg=white;options=bold>=== SPLASH : PING TEST FAILED </>"
        );
        $this->showLogs($output, $result);
    }
}
