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

    protected function execute(InputInterface $Input, OutputInterface $Output)
    {       
        $this->Selftest($Input, $Output);
        $this->Ping($Input, $Output);
        $this->Connect($Input, $Output);
    }

    
}
    