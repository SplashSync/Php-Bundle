<?php

namespace Splash\Bundle\Models;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Splash\Client\Splash;

abstract class BaseCommand extends ContainerAwareCommand
{
    protected function selftest(InputInterface $Input, OutputInterface $Output)
    {
        //====================================================================//
        // Perform Connect Test
        $Result = Splash::Selftest();
        if ($Result) {
            $Output->writeln("<bg=green;fg=white;options=bold>=== SPLASH : SELF-TEST PASSED </>");
        } else {
            $Output->writeln("<bg=red;fg=white;options=bold>=== SPLASH : SEFL-TEST FAIL </>");
        }
        
        if (!$Result || $Output->isVerbose()) {
            $Output->write(Splash::log()->GetConsoleLog(true));
            $Output->writeln("");
            $Output->writeln("");
        }
    }
    
    protected function ping(InputInterface $Input, OutputInterface $Output)
    {
        //====================================================================//
        // Perform Ping Test
        $Result = Splash::Selftest();
        if ($Result) {
            $Output->writeln("<bg=green;fg=white;options=bold>=== SPLASH : PING TEST PASSED </>");
        } else {
            $Output->writeln("<bg=red;fg=white;options=bold>=== SPLASH : PING TEST FAIL </>");
        }
    }
    
    protected function connect(InputInterface $Input, OutputInterface $Output)
    {
        //====================================================================//
        // Perform Connect Test
        $Result = Splash::Connect();
        if ($Result) {
            $Output->writeln("<bg=green;fg=white;options=bold>=== SPLASH : CONNECT TEST PASSED </>");
        } else {
            $Output->writeln("<bg=red;fg=white;options=bold>=== SPLASH : CONNECT TEST FAIL </>");
        }
        
        $this->ShowLogs($Output, $Result);
    }
    
    protected function showLogs(OutputInterface $Output, bool $Result = false)
    {
        if (!$Result || $Output->isVerbose()) {
            $Output->write(Splash::log()->GetConsoleLog(true));
            $Output->writeln("");
            $Output->writeln("");
        }
    }
}
