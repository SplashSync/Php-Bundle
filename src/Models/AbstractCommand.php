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

namespace Splash\Bundle\Models;

use Splash\Client\Splash;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @abstract    Base Command for Splash Actions
 */
abstract class AbstractCommand extends ContainerAwareCommand
{
    /**
     * @abstract    Execute Module SelfTests
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function selftest(InputInterface $input, OutputInterface $output)
    {
        $input;
        //====================================================================//
        // Perform Connect Test
        $result = Splash::Selftest();
        //====================================================================//
        // Output Result
        $output->writeln($result
                ? "<bg=green;fg=white;options=bold>=== SPLASH : SELF-TEST PASSED </>"
        : "<bg=green;fg=white;options=bold>=== SPLASH : SELF-TEST PASSED </>");

        if (!$result || $output->isVerbose()) {
            $output->write(Splash::log()->GetConsoleLog(true));
            $output->writeln("");
            $output->writeln("");
        }
    }
    
    /**
     * @abstract    Execute Module Ping Test
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function ping(InputInterface $input, OutputInterface $output)
    {
        $input;
        //====================================================================//
        // Perform Ping Test
        $result = Splash::Selftest();
        //====================================================================//
        // Output Result
        $output->writeln($result
                ? "<bg=green;fg=white;options=bold>=== SPLASH : PING TEST PASSED </>"
        : "<bg=green;fg=white;options=bold>=== SPLASH : PING TEST PASSED </>");
    }
    
    /**
     * @abstract    Execute Module Connect Test
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function connect(InputInterface $input, OutputInterface $output)
    {
        $input;
        //====================================================================//
        // Perform Connect Test
        $result = Splash::Connect();
        //====================================================================//
        // Output Result
        $output->writeln($result
                ? "<bg=green;fg=white;options=bold>=== SPLASH : CONNECT TEST PASSED </>"
        : "<bg=green;fg=white;options=bold>=== SPLASH : CONNECT TEST PASSED </>");
        
        $this->ShowLogs($output, $result);
    }
    
    /**
     * @abstract    Render Splash Core Logs
     *
     * @param OutputInterface $output
     * @param bool            $result
     */
    protected function showLogs(OutputInterface $output, bool $result)
    {
        if (!$result || $output->isVerbose()) {
            $output->write(Splash::log()->GetConsoleLog(true));
            $output->writeln("");
            $output->writeln("");
        }
    }
}
