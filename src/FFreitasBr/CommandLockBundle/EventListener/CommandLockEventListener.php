<?php

namespace FFreitasBr\CommandLockBundle\EventListener;

use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;
use FFreitasBr\CommandLockBundle\Exception\CommandAlreadyRunningException;
use FFreitasBr\CommandLockBundle\Traits\NamesDefinitionsTrait;

/**
 * Class CommandLockEventListener
 *
 * @package FFreitasBr\CommandLockBundle\EventListener
 */
class CommandLockEventListener extends ContainerAware
{
    use NamesDefinitionsTrait;

    /**
     * @var null|string
     */
    protected $pidDirectory = null;

    /**
     * @var null|string
     */
    protected $pidFile = null;
    
    /**
     * @param ContainerInterface $container
     *
     * @return self
     */
    public function __construct(ContainerInterface $container)
    {
        // set container
        $this->setContainer($container);
        // get the pid directory and store in self
        $this->pidDirectory = $container->getParameter($this->configurationsParameterKey)[$this->pidDirectorySetting];
    }

    /**
     * @param ConsoleCommandEvent $event
     *
     * @return void
     * @throws CommandAlreadyRunningException
     */
    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
        // generate pid file name
        $commandName        = $event->getCommand()->getName();
        $clearedCommandName = $this->cleanString($commandName);
        $pidFile = $this->pidFile = $this->pidDirectory . "/{$clearedCommandName}.pid";
        // check if command is already executing
        if (file_exists($pidFile)) {
            $pidOfRunningCommand = file_get_contents($pidFile);
            throw (new CommandAlreadyRunningException)
                ->setCommandName($commandName)
                ->setPidNumber($pidOfRunningCommand);
        }
        // if is not already executing create pid file
        file_put_contents($pidFile, getmypid());
        // register shutdown function to remove pid file in case of unexpected exit
        register_shutdown_function(
            function () use ($pidFile) {
                if (file_exists($pidFile)) {
                    unlink($pidFile);
                }
            }
        );
        // register callback function in case of receive the terminate signal
        if (function_exists('pcntl_signal')) {
            declare(ticks = 1);
            pcntl_signal(
                SIGTERM,
                function ($sigNumber) use ($pidFile) {
                    if (file_exists($pidFile)) {
                        unlink($pidFile);
                    }
                }
            );
        }
    }

    /**
     * @param ConsoleTerminateEvent $event
     * 
     * @return void
     */
    public function onConsoleTerminate(ConsoleTerminateEvent $event)
    {
        if (isset($this->pidFile) && file_exists($this->pidFile)) {
            unlink($this->pidFile);
        }
    }

    /**
     * @param $string
     *
     * @return mixed
     */
    public function cleanString($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }
}
