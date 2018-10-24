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
        $Input;
        //====================================================================//
        // Perform Connect Test
        $Result = Splash::Selftest();
        //====================================================================//
        // Output Result
        $Output->writeln($Result
                ? "<bg=green;fg=white;options=bold>=== SPLASH : SELF-TEST PASSED </>"
                : "<bg=green;fg=white;options=bold>=== SPLASH : SELF-TEST PASSED </>");

            
        if (!$Result || $Output->isVerbose()) {
            $Output->write(Splash::log()->GetConsoleLog(true));
            $Output->writeln("");
            $Output->writeln("");
        }
    }
    
    protected function ping(InputInterface $Input, OutputInterface $Output)
    {
        $Input;
        //====================================================================//
        // Perform Ping Test
        $Result = Splash::Selftest();
        //====================================================================//
        // Output Result
        $Output->writeln($Result
                ? "<bg=green;fg=white;options=bold>=== SPLASH : PING TEST PASSED </>"
                : "<bg=green;fg=white;options=bold>=== SPLASH : PING TEST PASSED </>");
    }
    
    protected function connect(InputInterface $Input, OutputInterface $Output)
    {
        $Input;
        //====================================================================//
        // Perform Connect Test
        $Result = Splash::Connect();
        //====================================================================//
        // Output Result
        $Output->writeln($Result
                ? "<bg=green;fg=white;options=bold>=== SPLASH : CONNECT TEST PASSED </>"
                : "<bg=green;fg=white;options=bold>=== SPLASH : CONNECT TEST PASSED </>");
        
        $this->ShowLogs($Output, $Result);
    }
    
    protected function showLogs(OutputInterface $Output, bool $Result)
    {
        if (!$Result || $Output->isVerbose()) {
            $Output->write(Splash::log()->GetConsoleLog(true));
            $Output->writeln("");
            $Output->writeln("");
        }
    }
}
