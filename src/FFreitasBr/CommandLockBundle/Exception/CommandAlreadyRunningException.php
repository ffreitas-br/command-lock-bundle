<?php

namespace FFreitasBr\CommandLockBundle\Exception;

/**
 * Class CommandAlreadyRunningException
 *
 * @package FFreitasBr\CommandLockBundle\Exception
 */
class CommandAlreadyRunningException extends \Exception
{
    /**
     * Main message string
     * @var string
     */
    protected $message = "Command \"unknown\" already running with pid \"unknown\"";

    /**
     * The message template, used to generate the main message string
     * @var string
     */
    protected $messageTemplate = "Command \"%s\" already running with pid \"%s\"";

    /**
     * Store the running command name
     * @var string
     */
    protected $commandName = 'unknown';

    /**
     * Store the pid number of command running
     * @var string
     */
    protected $pidNumber = 'unknown';

    /**
     * @param string $commandName
     *
     * @return self
     */
    public function setCommandName($commandName)
    {
        if (!isset($commandName)) {
            $commandName = 'unknown';
        }
        $this->commandName = $commandName;
        $this->updateMessage();
        return $this;
    }

    /**
     * @param int $pidNumber
     * 
     * @return self
     */
    public function setPidNumber($pidNumber)
    {
        if (!isset($pidNumber)) {
            $pidNumber = 'unknown';
        }
        $this->pidNumber = $pidNumber;
        $this->updateMessage();
        return $this;
    }
    
    /**
     * @return void
     */
    protected function updateMessage()
    {
        $this->message = sprintf($this->messageTemplate, $this->commandName, $this->pidNumber);
    }
}
