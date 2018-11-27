<?php

namespace Splash\Bundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Splash\Bundle\Models\BaseCommand;

class PingCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('splash:ping')
            ->setDescription('Splash : Perform Ping test')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->Ping($input, $output);
    }
}
