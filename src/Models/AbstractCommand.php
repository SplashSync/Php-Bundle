<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Models;

use Psr\Log\LoggerInterface;
use Splash\Bundle\Connectors\NullConnector;
use Splash\Bundle\Events\IdentifyServerEvent;
use Splash\Core\Client\Splash;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Base Command for Connector Actions
 */
abstract class AbstractCommand extends Command
{
    use Connectors\EventDispatcherAwareTrait;
    use Connectors\LoggerAwareTrait;

    /**
     * Current Connector for Action
     *
     * @var AbstractConnector
     */
    protected $connector;

    /**
     * Class Constructor
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface          $logger
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, LoggerInterface $logger)
    {
        //====================================================================//
        // Execute Parent Constructor
        parent::__construct(null);
        //====================================================================//
        // Init Dispatcher & Logger
        $this->setEventDispatcher($eventDispatcher);
        $this->setLogger($logger);
        //====================================================================//
        // Create Null Connector for Identification
        $this->connector = new NullConnector($eventDispatcher, $logger);
    }

    /**
     * Base Configuration for Connector Command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setDescription('Splash Generic Connector Command')
            ->addArgument('webserviceId', InputArgument::REQUIRED, 'WebService Id of the Connector Server')
        ;
    }

    /**
     * Initializes the command after the input has been bound and before the input
     * is validated.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        //==============================================================================
        // Use Sf Event to Identify Server
        $this->identify($input);
    }

    /**
     * Execute Console Command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return null|int
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //==============================================================================
        // Render Connector Basic Infos
        $this->showHello($output);
        $this->showProfile($output);
        $this->showConfiguration($output);
        $output->writeln('<info>------------------------------------------------------</info>');
        $output->writeln('This is default command action for Splash Connector.');
        $output->writeln('To build your own actions, just override the Execute function!');
        $output->writeln('<info>------------------------------------------------------</info>');

        return 0;
    }

    /**
     * Render Connector Informations in Console
     *
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function showHello(OutputInterface $output): void
    {
        $output->writeln('<info>');
        $output->writeln('------------------------------------------------------');
        $output->writeln('Hello '.$this->connector->getProfile()["name"]." Connector");
    }

    /**
     * Render Connector Informations in Console
     *
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function showProfile(OutputInterface $output): void
    {
        $output->writeln('------------------------------------------------------');
        $output->writeln('</info>');
        $output->writeln('Connector Profile');
        $output->writeln('<comment>'.print_r($this->connector->getProfile(), true).'</comment>');
    }

    /**
     * Render Connector Informations in Console
     *
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function showConfiguration(OutputInterface $output): void
    {
        $output->writeln('<info>------------------------------------------------------</info>');
        $output->writeln('Server Configuration');
        $output->writeln('<comment>'.print_r($this->connector->getConfiguration(), true).'</comment>');
    }

    /**
     * Identify of Server in Memory & Set it as Default Connector.
     *
     * @param InputInterface $input
     *
     * @throws LogicException           if no HelperSet is defined
     * @throws InvalidArgumentException When the Webserviec Id is invalid
     *
     * @return void
     */
    protected function identify(InputInterface $input): void
    {
        //==============================================================================
        // Safety Checks
        if (!$input->hasArgument('webserviceId') || !is_string($input->getArgument('webserviceId'))) {
            throw new InvalidArgumentException('Webservice Id is Empty or invalid.');
        }
        $webserviceId = $input->getArgument('webserviceId');
        //==============================================================================
        // Use Sf Event to Identify Server
        /** @var IdentifyServerEvent $event */
        $event = $this->getEventDispatcher()->dispatch(
            new IdentifyServerEvent($this->connector, $webserviceId)
        );
        //==============================================================================
        // Ensure Identify Server was Ok
        if (!$event->isIdentified()) {
            throw new LogicException(
                sprintf('Unable to Identify connector server %s. Is this the right Server?', $webserviceId)
            );
        }
        //==============================================================================
        // If Connection Was Rejected
        if ($event->isRejected()) {
            throw new LogicException(
                sprintf('Connection to connector server %s was Rejected. Is this Server Active?', $webserviceId)
            );
        }
        //====================================================================//
        // Server Found => Use Identified Connector Service
        $this->connector = $event->getConnector();
    }

    /**
     * Render Splash Core Logs
     *
     * @param OutputInterface $output
     * @param bool            $result
     *
     * @return void
     */
    protected function showLogs(OutputInterface $output, bool $result): void
    {
        if (!$result || $output->isVerbose()) {
            $output->write(Splash::log()->GetConsoleLog(true));
            $output->writeln("");
            $output->writeln("");
        }
    }
}
