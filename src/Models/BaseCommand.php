<?php

namespace Splash\Bundle\Models;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Splash\Client\Splash;

abstract class BaseCommand extends ContainerAwareCommand
{
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
    
    protected function showLogs(OutputInterface $output, bool $result)
    {
        if (!$result || $output->isVerbose()) {
            $output->write(Splash::log()->GetConsoleLog(true));
            $output->writeln("");
            $output->writeln("");
        }
    }
}
