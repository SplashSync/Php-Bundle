<?php

namespace Splash\Bundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Splash\Bundle\Models\BaseCommand;

class ConnectCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('splash:connect')
            ->setDescription('Splash : Perform Connect test')
        ;
    }

    protected function execute(InputInterface $Input, OutputInterface $Output)
    {
        $this->Connect($Input, $Output);
    }
}
