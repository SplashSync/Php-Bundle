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

namespace Splash\Bundle\Command;

use Splash\Bundle\Models\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @abstract    Splash Install Command
 */
class InstallCommand extends AbstractCommand
{
    /**
     * @abstract    Configure Symfony Command
     */
    protected function configure()
    {
        $this
            ->setName('splash:install')
            ->setDescription('Splash : Install Splash Client')
        ;
    }

    /**
     * @abstract    Execute Symfony Command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->Selftest($input, $output);
        $this->Ping($input, $output);
        $this->Connect($input, $output);
    }
}
