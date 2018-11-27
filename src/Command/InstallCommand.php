<?php

namespace Splash\Bundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Splash\Bundle\Models\BaseCommand;

class InstallCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('splash:install')
            ->setDescription('Splash : Install Splash Client')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->Selftest($input, $output);
        $this->Ping($input, $output);
        $this->Connect($input, $output);
    }
}
