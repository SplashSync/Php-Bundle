<?php

namespace Splash\Bundle\Models;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Splash\Client\Splash;

abstract class BaseCommand extends ContainerAwareCommand
{

    protected function Selftest(InputInterface $Input, OutputInterface $Output)
    {       
        //====================================================================//
        // Perform Connect Test 
        $Result = Splash::Selftest();
        if ( $Result ) {
            $Output->writeln("<bg=green;fg=white;options=bold>=== SPLASH : SELF-TEST PASSED </>");
        } else {
            $Output->writeln("<bg=red;fg=white;options=bold>=== SPLASH : SEFL-TEST FAIL </>");            
        }
        
        if ( !$Result || $Output->isVerbose() ) {
            $Output->write(Splash::Log()->GetConsoleLog(True));            
            $Output->writeln("");            
            $Output->writeln("");            
        }
    }
    
    protected function Ping(InputInterface $Input, OutputInterface $Output)
    {       
        //====================================================================//
        // Perform Ping Test        
        $Result = Splash::Selftest();
        if ( $Result ) {
            $Output->writeln("<bg=green;fg=white;options=bold>=== SPLASH : PING TEST PASSED </>");
        } else {
            $Output->writeln("<bg=red;fg=white;options=bold>=== SPLASH : PING TEST FAIL </>");            
        }
    }
    
    protected function Connect(InputInterface $Input, OutputInterface $Output)
    {       
        //====================================================================//
        // Perform Connect Test        
        $Result = Splash::Connect();
        if ( $Result ) {
            $Output->writeln("<bg=green;fg=white;options=bold>=== SPLASH : CONNECT TEST PASSED </>");
        } else {
            $Output->writeln("<bg=red;fg=white;options=bold>=== SPLASH : CONNECT TEST FAIL </>");            
        }
        
        $this->ShowLogs($Output, $Result);
    }
    
    protected function ShowLogs( OutputInterface $Output, bool $Result = False)
    {       
        if ( !$Result || $Output->isVerbose() ) {
            $Output->write(Splash::Log()->GetConsoleLog(True));            
            $Output->writeln("");            
            $Output->writeln("");            
        }
    }
    
}
    